<?php

// src/Tornado/ApiBundle/Controller/ApiController.php
namespace Tornado\ApiBundle\Controller;

// Use Tornado entities.
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Tornado\ApiBundle\Entity\Resource;

class ApiController extends BaseApiController
{
  public function getRepository()
  {
    return $this->getDoctrine()->getManager()->getRepository('TornadoApiBundle:Resource');
  }

  public function getNewEntity()
  {
    return new Resource;
  }

  public function uploadFileAction(Request $request)
  {
    $file = reset($request->files->all()['form']);

    $resource = $this->getNewEntity();
    $resource->setFile($file)->uploadFile()->build();

    $em = $this->getDoctrine()->getManager();
    $em->persist($resource);

    $em->flush();

    return new JsonResponse($this->getEntityForJson($resource->getId()));
  }

  public function uploadSourceAction(Request $request)
  {
    $source = $request->request->get('code', '');

    $resource = $this->getNewEntity();
    $resource->setSource($source)->uploadSource()->build();

    $em = $this->getDoctrine()->getManager();
    $em->persist($resource);
    $em->flush();

    return new JsonResponse(array('id' => $resource->getId()));
  }
}
