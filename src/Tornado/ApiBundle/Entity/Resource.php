<?php

namespace Tornado\ApiBundle\Entity;

// Some JMS Serializer Annotation classes to help prepare the object.
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;

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
  private function getFile()
  {
    return $this->file;
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
}
