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
      }

      return $this->render('TornadoUserBundle:Default:index.html.twig', array(
          'token' => $token,
          'user' => $user,
          'repo' => $repo,
        )
      );
    }

    public function loginAction(Request $request)
    {
      return $this->render("TornadoUserApi:Default:login.html.twig");
    }

    public function dashboardAction(Request $request)
    {
      $user = $this->get('session')->get('user');
      $user = new User();
      $user->setUsername('steveworley');
      $user->setName('Steve Worley');
      $user->setEmail('sj.worley88@gmail.com');
      $user->setTokens(array('github' => '10522c8f0ea67b82b004b07a0c8b3996d7e397b4'));


      // if (!$user) {
      //   // If we don't have a user object return the user to the login page.bash
      //   return $this->redirect($this->generateUrl('tornado_user_login'));
      // }

      return $this->render("TornadoUserBundle:Default:dashboard.html.twig", array(
          'menu' => '',
          'user' => $user,
        )
      );
    }

    public function _newAction()
    {
      return $this->render('TornadoUserBundle:Default:new.html.twig');
    }

    public function _dashboardAction()
    {
      return $this->render('TornadoUserBundle:Default:new_user.html.twig');
    }
}
