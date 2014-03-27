<?php
// src/Tornado/SiteBundle/Controller/PageController.php
namespace Tornado\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use Tornado\ApiBundle\Entity\Resource;
use Tornado\ApiBundle\Controller\BaseApiController;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


class PageController extends BaseApiController
{
  public function getRepository()
  {
    return $this->getDoctrine()->getManager()->getRepository('TornadoApiBundle:Resource');
  }

  public function getNewEntity()
  {
    return new Resource;
  }

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

  public function getUploadForm()
  {
    $form = $this->createFormBuilder($this->getNewEntity());

    $form->setAction($this->generateUrl('tornado_api_upload_file'))
      ->add('file', 'file', array('label' => null))
      ->add('upload', 'submit');

    return $form;
  }

  public function getSourceForm()
  {
    $form = $this->createFormBuilder($this->getNewEntity());

    $form->setAction($this->generateUrl('tornado_api_upload_source'))
      ->add('code', 'textarea', array('label' => null))
      ->add('Send source code', 'submit');

    return $form;
  }

  public function indexAction(Request $request)
  {
    return $this->render('TornadoSiteBundle:Page:index.html.twig', array(
      'menu' => $this->getPageMenu(),
      'form' => $this->getUploadForm()->getForm()->createView(),
    ));
  }

  /**
   * Show documentation.
   *
   * @return string
   */
  public function documentationAction()
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
