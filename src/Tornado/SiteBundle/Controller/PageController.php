<?php
// src/Tornado/SiteBundle/Controller/PageController.php
namespace Tornado\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Tornado\ApiBundle\Entity\Resource;
use Tornado\ApiBundle\Controller\BaseApiController;
use Tornado\ApiBundle\Entity\Revision;

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
    return new Resource();
  }

  /**
   * Get the forms for a specific type.
   */
  public function getForms($type)
  {
    $forms = array(
      'file' => $this->createForm(new UploadFileType),
      'code' => $this->createForm(new SourceCodeType),
    );

    // Build the form view.
    return $forms[$type]->createView();
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

    return $this->render('TornadoSiteBundle:Page:index.html.twig', array(
      'menu' => $this->getPageMenu(),
      'uploadFile' => $this->getForms('file'),
      'uploadSource' => $this->getForms('code'),
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
    if (!$resource = $this->getEntity($id)) {
      throw $this->createNotFoundException("No resource with $id has been found.");
    }

    return $this->render('TornadoSiteBundle:Page:resource.html.twig', array(
      'menu' => $this->getPageMenu(),
      'Resource' => $resource,
      'File' => $resource->loadSourceFile(),
      'revisionUploadForm' => $this->getForms('file'),
      'revisionSourceCodeForm' => $this->getForms('code'),
    ));
  }
}
