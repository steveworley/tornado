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
    return $this->render('TornadoSiteBundle:Page:documentation.html.twig', array(
      'menu' => $this->getPageMenu(),
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
