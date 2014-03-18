<?php

// src/Tornado/ApiBundle/Controller/ApiController.php
namespace Tornado\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Use Tornado entities.
use Tornado\ApiBundle\Entity\Resource;

class ApiController extends Controller
{

  public function fileAction(Request $request)
  {
    $resource = new Resource;
    $file = reset($request->files->all()['form']);

    $manager = $this->getDoctrine()->getManager();

    // Set the posted file to a new resource object.
    $resource
      ->setFile($file)
      ->upload()
      ->buildOutput();

    $manager->persist($resource);
    $manager->flush();

    $data = array(
      'status' => 'success',
      'message' => 'Successfully uploaded a file',
      'resource' => $resource->to('json'),
    );

    $response = new Response(json_encode($data));
    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }

  public function codeAction(Request $request)
  {

  }

}
