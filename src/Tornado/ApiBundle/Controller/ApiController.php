<?php
// src/Tornado/ApiBundle/Controller/ApiController.php

namespace Tornado\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

// Use Tornado entities.
use Tornado\ApiBundle\Entity\Resource;
use Tornado\ApiBundle\Entity\Revision;

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
    return new Resource();
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
   * Create a resource from the request.
   */
  public function createResource(Request $request)
  {
    $file = $this->getFileFromRequest($request);

    $filePath = $this->get('tornado_api.file_manager')->setFile($file)->createFile();
    $resource = $this->getNewEntity();

    foreach ($this->get('tornado_api.command_bag')->getCommands() as $command) {
      $command->setFile($filePath)->run($resource);
    }

    $resource->setFile($filePath)
      ->setCreated(new \DateTime)
      ->setId($this->get('tornado_api.file_manager')->getFileName())
      ->setTotal($resource->calculateComplexity());

    return $resource;
  }

  /**
   * Handle an upload request.
   */
  public function uploadAction(Request $request)
  {
    $resource = $this->createResource($request);
    $this->persist($resource);

    return $this->sendResponse($resource->getId());
  }

  /**
   * Create a revision of the current resource.
   */
  public function revisionCreateAction(Request $request)
  {
    if (!($rid = $request->request->get('rid'))) {
      throw $this->createNotFoundException("No resource found.");
    }

    // Make sure we have access to the entity manager.
    $em = $this->getDoctrine()->getManager();

    // Get a copy of the old Resource.
    $base_resource = $em->getRepository('TornadoApiBundle:Resource')->find($rid);

    // Build a new revision.
    $revision = new Revision;
    $revision->setResourceId($base_resource)->setEntity($base_resource);
    $em->persist($revision);

    // Create a new resource with the given data.
    $resource = $this->createResource($request);

    // Update the base resource.
    $base_resource
      ->setFile($resource->getFile())
      ->setCreated($resource->getCreated())
      ->setTotal($resource->getTotal());
      // ->setRevisions($revision);

    // Flushing the entity manager will persist the updated entity.
    $em->persist($base_resource);
    $em->flush();

    // Send the Resource ID back so we can update
    return $this->sendResponse($rid);
  }
}
