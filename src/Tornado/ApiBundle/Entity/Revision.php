<?php

namespace Tornado\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

// Some JMS Serializer Annotation classes to help prepare the object.
use JMS\Serializer\SerializerBuilder;
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
    private $entity;

    /**
     * @var string
     * @Type("string")
     * @Expose
     */
    private $resource;

    /**
     * @var string
     * @Type("string")
     */
    private $resource_id;

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
     * Setter for \entity
     *
     * @param string $entity
     *   A Resource ID which contains alphanumeric characters.
     *
     * @return Revision
     */
    public function setEntity($entity)
    {
      $serializer = SerializerBuilder::create()->build();
      $this->entity = $serializer->serialize($entity, 'json');

      return $this;
    }

    /**
     * Getter for \entity
     *
     * @return string
     */
    public function getEntity()
    {
      $serializer = SerializerBuilder::create()->build();

      return $serializer->deserialize($this->entity, 'Tornado\ApiBundle\Entity\Resource', 'json');
    }


    /**
     * Setter for .
     *
     * @param Resource
     *   The to revise.
     *
     * @return Revision
     */
    public function setResource($resource)
    {
      // Serializing a Resource will allow us to store all the values in the
      // database for this resource.
      $this->resource = $resource;

      return $this;
    }

    /**
     * Getter for \resource.
     *
     * @return Resource
     */
    public function getResource()
    {
      return $this->resource;
    }

    public function setResourceId($resource_id)
    {
      $this->resource_id = $resource_id;
      return $this;
    }

    public function getResourceId()
    {
      return $this->resource_id;
    }

    public function __toString()
    {
      return "Revision for: $this->entity";
    }
}
