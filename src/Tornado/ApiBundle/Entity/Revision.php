<?php

namespace Tornado\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

// Some JMS Serializer Annotation classes to help prepare the object.
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;

/**
 * Revision
 */
class Revision
{
    /**
     * @var integer
     * @Type("integer")
     * @Expose
     */
    private $id;

    /**
     * @var string
     * @Type("string")
     * @Expose
     */
    private $resource_id;

    /**
     * @var string
     * @Type("string")
     * @Expose
     */
    private $entity;

    /**
     * @var Resource
     */
    protected $resource;

    /**
     * Setter for \id
     *
     * @param int $id
     *   An integer to set for this ID.
     *
     * @return Revision
     */
    public function setId($id)
    {
      $this->id = $id;

      return $this;
    }

    /**
     * Getter for \id.
     *
     * @return int
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * Setter for \resource_id
     *
     * @param string $resource_id
     *   A Resource ID which contains alphanumeric characters.
     *
     * @return Revision
     */
    public function setResourceId($resource_id)
    {
      $this->resource_id = $resource_id;

      return $this;
    }

    /**
     * Getter for \resource_id
     *
     * @return string
     */
    public function getResourceId()
    {
      return $this->resource_id;
    }


    /**
     * Setter for \entity.
     *
     * @param Resource $entity
     *   The entity to revise.
     *
     * @return Revision
     */
    public function setEntity($entity)
    {
      // Serializing a Resource will allow us to store all the values in the
      // database for this entity.
      $this->entity = serialize($entity);

      return $this;
    }

    /**
     * Getter for \entity.
     *
     * @return Resource
     */
    public function getEntity()
    {
      return unserialize($this->entity);
    }
}
