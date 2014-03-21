<?php

namespace Tornado\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

/**
 * Resource
 */
class Resource
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var string
     */
    private $output;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var path
     */
    private $path;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return Resource
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set output
     *
     * @param string $output
     * @return Resource
     */
    public function setOutput($output)
    {
        $this->output = json_encode($output);

        return $this;
    }

    /**
     * Get output
     *
     * @return string
     */
    public function getOutput()
    {
        return json_decode($this->output, TRUE);
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
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get path
     *
     * @return string;
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set path
     *
     * @param string path
     * @return Resource
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
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
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get \AbsolutePath
     *
     * @return string
     */
    public function getAbsolutePath()
    {
        return NULL === $this->path
            ? NULL
            : $this->getUploadDir() . '/' . $this->path;
    }

    /**
     * Get \WebPath
     *
     * @return string
     */
    public function getWebPath()
    {
        return NULL === $this->path
            ? NULL
            : $this->getUploadDir() . '/' . $this->path;
    }

    /**
     * Get \UploadRootDir
     *
     * @return string
     */
    private function getUploadRootDir()
    {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    /**
     * Get \UploadDir
     *
     * @return string
     */
    private function getUploadDir()
    {
        return 'uploads/resources';
    }

    /**
     * Upload a file to the server.
     *
     * @return Resource
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // A unique dir.
        $uniqueDir = substr(md5(time()), 0, 8);

        $this->getFile()->move(
            $this->getUploadDir() . '/' . $uniqueDir,
            $this->getFile()->getClientOriginalName()
        );

        $this->path = $uniqueDir . '/' . $this->getFile()->getClientOriginalName();
        $this->file = null;

        return $this;
    }

    /**
     * Build an output array.
     *
     * @return array
     *   PHPLoc output converted to a PHP array.
     */
    public function buildFromFile()
    {
        if (NULL === $this->getAbsolutePath()) {
            return;
        }

        $outputPath = explode('/', $this->getPath());

        // Perform phploc over the uploaded file.
        exec("php ~/Scripts/phploc.phar " . $this->getAbsolutePath() . " > " . $this->getUploadDir() . '/' . reset($outputPath) . '/phploc.out');

        try {
            // Get the output file so we can cast it to any array.
            $phploc_output = file_get_contents($this->getUploadDir() . '/' . reset($outputPath) . '/phploc.out');
        } catch(ContextErrorException $e) {
            throw new Exception("Something went wrong. Please upload your file again.");
        }

        $string = str_replace('phploc 2.0.4 by Sebastian Bergmann.', '', $phploc_output);
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

        $this->setOutput($output)
            ->setCreated($date)
            ->setHash(substr(md5($this->getPath()), 0, 5));

        return $this;
    }

    public function buildFromCode($source) {
        $source = "<?php \n\n" . $source;
        $dir = substr(md5(time()), 0, 8);
        $filename = "$dir/code.php";

        exec("mkdir " . $this->getUploadRootDir() . "/$dir");
        exec("touch " . $this->getUploadDir() . "/$filename");
        file_put_contents($this->getUploadRootDir() . "/$filename", $source, FILE_APPEND | LOCK_EX);

        $this->setPath($filename);
        return $this->buildFromFile();
    }

    public function to($type) {
        $encoders = array(new XmlEncoder(), new JsonEncoder());

        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->serialize($this, $type);
    }
}
