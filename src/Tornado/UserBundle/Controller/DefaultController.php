<?php

namespace Tornado\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tornado\UserBundle\Models\User;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('TornadoUserBundle:Default:index.html.twig', array('name' => $name));
    }

    public function newAction(Request $request)
    {
      if ($request->query->get('code')) {
        // $token = $this->get('tornado.oauth.github')->getAccessToken($request);
        $token = "10522c8f0ea67b82b004b07a0c8b3996d7e397b4";
        $user = $this->get('tornado.oauth.github')->getUserData($token);
        $repo = $this->get('tornado.oauth.github')->getRepositories($token);
      }

      return $this->render('TornadoUserBundle:Default:index.html.twig', array(
          'token' => $token,
          'user' => $user,
          'repo' => $repo,
        )
      );
    }
}
