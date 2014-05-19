<?php

namespace Tornado\ApiBundle\Entity;

// Some JMS Serializer Annotation classes to help prepare the object.
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;

// Used for oneToMany relationship.
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ExclusionPolicy("all")
 */
class Resource
{
  /**
   * @var string
   * @Type("string")
   * @Expose
   */
  public $id;

  /**
   * @var string
   * @Type("string")
   * @Expose
   */
  public $complexity;

  /**
   * @var \DateTime
   * @Expose
   */
  public $created;

  /**
   * @var string
   */
  private $file;

  /**
   * @var integer
   * @Type("integer")
   * @Expose
   */
  public $total;

  /**
   * @var ArrayCollection
   */
  protected $revisions;

  /**
   * Cosntruct a resource and inisitalize the revisions.
   */
  public function __construct() {
    $this->revisions = new ArrayCollection();
  }

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
   * Set id
   *
   * @param uniqId
   * @return Resource
   */
  public function setId($id)
  {
    $this->id = $id;

    return $this;
  }

  /**
   * Set complexity
   *
   * @param array $complexity
   * @return Resource
   */
  public function setComplexity($complexity)
  {
    $this->complexity = is_array($complexity) ? json_encode($complexity) : $complexity;
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
    return json_decode($this->complexity, TRUE);
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
   * @return date
   */
  public function getCreated()
  {
    return $this->created;
  }

  /**
   * Set file
   *
   * @param UploadedFile file
   * @return Resource
   */
  public function setFile($file)
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
   * Set the Total.
   *
   * @param float $total
   *
   * @return Resource
   */
  public function setTotal($total)
  {
    $this->total = $total;

    return $this;
  }

  /**
   * Get the total.
   *
   * @return float
   */
  public function getTotal()
  {
    return $this->total;
  }

  /**
   * Set \revisions
   *
   * @param ArrayCollection $revisions
   *  A collection of revisions relating to this resource.
   *
   * @return Resource
   */
  public function setRevisions($revisions)
  {
    $this->revisions = $revisions;

    return $this;
  }

  /**
   * Get all revision related to this Resource.
   */
  public function getRevisions()
  {
    return $this->revisions;
  }

  /**
   * Global setter.
   *
   * This will attempt to set any given property on this resource. It requires
   * that an associated setter has been defined.
   */
  public function set($property, $value)
  {
    $callable = "set" . ucfirst($property);

    if (is_callable(array($this, $callable))) {
      call_user_func(array($this, $callable), $value);
      return $this;
    }

    return FALSE;
  }

  /**
   * Attempt to return the output of the a source file.
   *
   * @return string
   */
  public function loadSourceFile()
  {
    if (null === $this->getFile() || !file_exists($this->getFile())) {
      throw new \Exception("Cannot load source file @location" . $this->getFile());
    }

    return file_get_contents($this->getFile());
  }

  /**
   * Calculate total file complexity.
   *
   * @return float
   */
  public function calculateComplexity()
  {
    $complexity = $this->getComplexity();
    $number = $complexity['Complexity']['Cyclomatic Complexity / LLOC'] * $complexity['Size']['Logical Lines of Code (LLOC)'];
    return number_format($number, 2);
  }

  /**
   * Get the saved file name for this resource.
   *
   * @return string
   */
  public function getFilename()
  {
    $file = $this->getFile();
    $file = explode('/', $file);
    return end($file);
  }
}
