<?php
// src/Tornado/ApiBundle/Controller/ApiController.php

namespace Tornado\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

// Use Tornado entities.
use Tornado\ApiBundle\Entity\Resource;

class ApiController extends BaseApiController
{

  /**
   * Define which repository the base class should get.
   */
  public function getRepository()
  {
    return $this->getDoctrine()->getManager()->getRepository('TornadoApiBundle:Resource');
  }

  /**
   * Define what a "new entity" is for the base class.
   */
  public function getNewEntity()
  {
    return new Resource;
  }

  /**
   * Handle a file upload request.
   */
  public function uploadFileAction(Request $request)
  {
    $file = reset($request->files->all()['form']);

    $resource = $this->getNewEntity();
    $resource->setFile($file)->uploadFile()->build();

    $this->persist($resource);

    return new JsonResponse($this->getEntityForJson($resource->getId()));
  }

  /**
   * Handle a source code request.
   */
  public function uploadSourceAction(Request $request)
  {
    $source = $request->request->get('code', '');

    $resource = $this->getNewEntity();
    $resource->setSource($source)->uploadSource()->build();

    $this->persist($resource);

    return new JsonResponse($this->getEntityForJson($resource->getId()));
  }
}
