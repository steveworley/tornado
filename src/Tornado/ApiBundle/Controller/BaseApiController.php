<?php

// src/Tornado/ApiBundle/Controller/BaseApiController.php
namespace Tornado\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query;
use Doctrine\ORM\NoResultException;

abstract class BaseApiController extends Controller
{
  abstract function getRepository();
  abstract function getNewEntity();

  protected function listAction()
  {
    $list = $this->getRepository()
      ->createQueryBuilder('e')
      ->getQuery()->getResult(Query::HYDRATE_ARRAY);

    return new JsonResponse($list);
  }

  protected function readAction($id, $field = 'hash')
  {
    $entityInstance = $this->getEntityForJson($id, $field);
    if (false === $entityInstance) {
      return $this->createNotFoundException();
    }

    return new JsonResponse($entityInstance);
  }

  protected function createAction()
  {
    $json = $this->getJsonFromRequest();
    if (false === $json) {
      throw new \Exception('Invalid JSON');
    }

    $object = $this->updateEntity($this->getNewEntity(), $json);
    if (false === $object) {
      throw new \Exception("Unable to create the entity");
    }

    $this->persist($object);

    return new JsonResponse($this->getEntityForJson($object->getId()));
  }

  protected function persist($object)
  {
    $em = $this->getDoctrine()->getManager();
    $em->persist($object);
    $em->flush();

    return $this;
  }

  protected function updateAction($id)
  {
    $object = $this->getEntity($id);
    if (false === $object) {
      return $this->createNotFoundException();
    }

    $json = $this->getJsonFromRequest();
    if (false === $json) {
      throw new \Exception('Invalid JSON');
    }

    if (false === $this->updateEntity($object, $json)) {
      throw new \Exception('Unable to update the entity');
    }
  }

  protected function deleteAction($id)
  {
    $object = $this->getEntity($id);
    if (false === $object) {
      return $this->createNotFoundException();
    }

    $em = $this->getDoctrine()->getManager();
    $em->remove($object);
    $em->flush();

    return new JsonResponse(array());
  }

  protected function getEntity($id)
  {
    try {
      return $this->getRepository()->find($id);
    }
    catch (NoResultException $exception) {
      return false;
    }

    return false;
  }

  protected function getEntityForJson($id)
  {
    try {
      return $this->getRepository()->createQueryBuilder('e')
        ->where('e.id = :id')
        ->setParameter('id', $id)
        ->getQuery()->getSingleResult(Query::HYDRATE_ARRAY);
    }
    catch (NoResultException $exception) {
      return array('status' => 'error', 'message' => 'Could not load entity.');
    }

    return false;
  }

  protected function getJsonFromRequest()
  {
    $json = $this->get("request")->getContent();
    if (!$json) {
      return false;
    }

    return $json;
  }

  protected function updateEntity($entity, $json)
  {
    $data = json_decode($json);
    if ($data === null) {
      return false;
    }

    foreach ($data as $name => $value) {
      if ($name != 'id') {
        $setter = array($entity, "set" . ucfirst($name));
        if (is_callable($setter)) {
          call_user_func_array($setter, array($value));
        }
      }
    }

    return $entity;
  }
}
