<?php

namespace Tornado\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

// Support file uploads.
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

// Use the Symfony filesystem component.
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class Resource
{

  /**
   * @var string
   */
  constant UPLOAD_PATH = '/../../../../secure';

  /**
   * @var phploc
   */
  constant PHPLOC = 'php ~/Scripts/phploc.phar';

  /**
   * @var string
   */
  private $id;

  /**
   * @var string
   */
  private $complexity;

  /**
   * @var \DateTime
   */
  private $created;

  /**
   * @var string
   * @Assert\File(maxSize="600000")
   */
  private $file;

  /**
   * Get id
   *
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set complexity
   *
   * @param array $complexity
   * @return Resource
   */
  public function setComplexity($complexity)
  {
    $this->complexity = json_encode($complexity);
    return $this;
  }

  /**
   * Get complexity
   *
   * @return array
   *   An array describing the output of PHPLoc.
   */
  public function getComplexity()
  {
    return json_decode($this->complexity);
  }

  /**
   * Set created
   *
   * @param \DateTime $created
   * @return Resource
   */
  public function setCreated($created)
  {
    $this->created = $created;
    return $this;
  }

  /**
   * Get path
   *
   * @return string
   */
  private function getPath()
  {
    return self::UPLOAD_PATH;
  }

  /**
   * Set file
   *
   * @param UploadedFile file
   * @return Resource
   */
  public function setFile(UploadedFile $file = null)
  {
    $this->file = $file;

    return $this;
  }

  /**
   * Get file
   *
   * @return File
   */
  private function getFile()
  {
    return $this->file;
  }

  /**
   * Set source
   *
   * @param string source
   * @return Resource
   */
  public function setSource($source = null)
  {
    $this->source = $source;

    return $this;
  }

  /**
   * Get source
   *
   * @return string
   */
  private function getSource()
  {
    return $this->source;
  }

  public function loadSourceFile()
  {
    if (null === $this->getId())
    {
      throw new \Exception("Cannot load source file");
    }

    $file_path = self::UPLOAD_PATH . '/' . $this->getId() . '/code.php';
    if (!file_exists($file_path)) {
      throw new \Exception("Cannot load source file");
    }

    return file_get_contents($file_path);
  }

  /**
   * Get a unique directory name
   *
   * @return string
   */
  public function generateUniqueDirectory()
  {
    return substr(uniqid()), 0, 8);
  }

  /**
   * Handle a file upload for a resource.
   * @return Resource
   */
  public function uploadFile()
  {
    if (null === $this->getFile()) {
      throw new \Exception("No file found");
    }

    // Give the id field a unique value.
    $this->id = $this->generateUniqueDirectory();

    $this->getFile()->move(
      $this->getPath() . '/' . $this->getId(),
      "code.php",
    );

    $this->setFile();

    return $this;
  }

  public function uploadSource()
  {
    if (null === $this->getSource()) {
      throw new \Exception("No source found");
    }
    $this->id = $this->generateUniqueDirectory();

    $fs = new Filesystem();
    $directory = self::UPLOAD_PATH . '/' . $this->getId();

    $fs->mkdir($directory, 0700);
    $fs->touch("{$directory}/code.php");
    $fs->dumpFile("{$directory}/code.php", $this->getSource());

    $this->setSource();

    return $this;
  }

  /**
   * Build the remaining fields for the entity.
   *
   * @return Resource
   */
  public function build()
  {
    $path = self::UPLOAD_PATH . '/' . $this->getId() . '/code.php';
    $output = passthru(self::PHPLOC . " $path");

    if (null === $output) {
      throw new \Exception("Cannot lint file");
    }

    $string = str_replace('phploc 2.0.4 by Sebastian Bergmann.', '', $output);
    $prev_item = NULL;
    $output = array();

    foreach (explode("\n", $string) as $item) {
      if (!preg_match("/^(\s)/", $item)) {
        $prev_item = trim($item);
      }

      else {
        // We need to remove leading and trailing whitespace.
        $value = trim($item);
        // Lines are split at multiple spaces.
        $value = preg_split("/\s\s([\s]+)?/", $value);

        // Enusre that we only have data that we can use.
        if (count($value) == 2) {
          $output[$prev_item][$value[0]] = $value[1];
        }
      }
    }

    $date = date_create("now", timezone_open('Australia/Brisbane'));
    $this->setComplexity($output)->setCreated($date);

    return $this;
  }
}
