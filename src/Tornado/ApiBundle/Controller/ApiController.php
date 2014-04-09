<?php
// src/Tornado/ApiBundle/Controller/ApiController.php

namespace Tornado\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

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
   * Locate file from request payload.
   *
   * This function will attempt to locate a payload item suitable to use as the
   * base of a new Resource. This will attempt to take a $_FILE or a string
   * send via $_POST and will return them.
   *
   * If this function does not locate a suitable sent object it will terminate
   * throw a 404.
   *
   * @see createNotFoundException
   */
  public function getFileFromRequest(Request $request)
  {
    $file = $request->files->all();

    if (empty($file)) {
      $file = $request->request->get('resource');
      $file = $file['code'];
    } else {
      $file = reset($file);
      $file = $file['file'];
    }

    if (empty($file)) {
      throw $this->createNotFoundException("Cannot locate requested file");
    }

    return $file;
  }

  /**
   * Handle a file upload request.
   */
  public function uploadAction(Request $request)
  {
    $file = $this->getFileFromRequest($request);

    $filePath = $this->get('tornado_api.file_manager')->setFile($file)->createFile();
    $resource = $this->getNewEntity();

    foreach ($this->get('tornado_api.command_bag')->getCommands() as $command) {
      $command->setFile($filePath)->run($resource);
    }

    $resource->setFile($filePath)
      ->setCreated(new \DateTime)
      ->setId($this->get('tornado_api.file_manager')->getFileName());

    $this->persist($resource);

    return $this->sendResponse($resource->getId());
  }
}
