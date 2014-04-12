<?php
// src/Tornado/SiteBundle/Controller/PageController.php
namespace Tornado\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Tornado\ApiBundle\Entity\Resource;
use Tornado\ApiBundle\Controller\BaseApiController;

// Use form types
use Tornado\SiteBundle\Forms\Type\SourceCodeType;
use Tornado\SiteBundle\Forms\Type\UploadFileType;
use Guzzle\Http\Client;

class PageController extends BaseApiController
{

  /**
   * Define how to get a repository that is related to this controller.
   */
  public function getRepository()
  {
    return $this->getDoctrine()->getManager()->getRepository('TornadoApiBundle:Resource');
  }

  /**
   * Define a new entity.
   */
  public function getNewEntity()
  {
    return new Resource;
  }

  /**
   * Build a page menu.
   */
  public function getPageMenu()
  {
    $current_path = $this->get('request')->getPathInfo();

    $menu = array();
    $menu[] = array(
      'label' => 'About',
      'path' => '/'
    );
    $menu[] = array(
      'label' => 'Documentation',
      'path' => '/docs',
    );

    foreach ($menu as &$item) {
      if ($item['path'] == $current_path) {
        $item['active'] = TRUE;
      }
    }

    return $menu;
  }
  /**
   * Handle an index request.
   */
  public function indexAction(Request $request)
  {
    $forms = array(
      $this->createForm(new UploadFileType),
      $this->createForm(new SourceCodeType),
    );

    return $this->render('TornadoSiteBundle:Page:index.html.twig', array(
      'menu' => $this->getPageMenu(),
      'uploadFile' => $forms[0]->createView(),
      'uploadSource' => $forms[1]->createView(),
    ));
  }

  /**
   * Show documentation.
   *
   * @return string
   */
  public function documentationAction(Request $request)
  {
    if (($code = $request->query->get('code'))) {
      $token = $this->get('tornado.oauth.github')->getAccessToken($request);

      $body = $token;

      // $oauth = "https://github.com/login/oauth/access_token";
      // $client = new Client();
      // $request = $client->post($oauth, null, array(
      //     'client_id' => 'b295da66e5aab5e8712a',
      //     'client_secret' => '9e90f83c7fc56cb07430f45b04043fc972101586',
      //     'code' => $_GET['code'],
      //   )
      // );
      // $response = $request->send();
      // $body = $response->getBody(TRUE);
      // $body = explode("&", $body);
      // foreach ($body as &$b) {
      //   $b = explode('=', $b);
      //   if (count($b) == 2) {
      //     $output[$b[0]] = $b[1];
      //   }
      // }
      // $api = $client->get("https://api.github.com/user?access_token=" . $output['access_token']);
      // $body = $api->send();
      // $body = $body->json();
      // $this->get('request')->getSession()->set('user', $body);
    } else {
      $body = $this->get('tornado.oauth.github');
    }
    return $this->render('TornadoSiteBundle:Page:documentation.html.twig', array(
      'menu' => $this->getPageMenu(),
      'body' => $body,
    ));
  }

  /**
   * Show a resource.
   *
   * @param string $id
   *   The ID of a resource to show.
   * @return string
   */
  public function showAction($id)
  {
    $resource = $this->getEntity($id);

    return $this->render('TornadoSiteBundle:Page:resource.html.twig', array(
      'menu' => $this->getPageMenu(),
      'Resource' => $resource,
      'File' => $resource->loadSourceFile(),
    ));
  }
}
