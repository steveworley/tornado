<?php
// src/Tornado/SiteBundle/Controller/PageController.php
namespace Tornado\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Tornado\ApiBundle\Entity\Resource;

class PageController extends Controller
{

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

  public function indexAction(Request $request)
  {
    $resource = new Resource;

    $form = $this->createFormBuilder($resource)
      ->setAction($this->generateUrl('tornado_api_file'))
      ->add('file')
      ->add('upload', 'submit')
      ->getForm();

    return $this->render('TornadoSiteBundle:Page:index.html.twig', array(
      'menu' => $this->getPageMenu(),
      'form' => $form->createView(),
    ));
  }

  public function documentationAction()
  {
    return $this->render('TornadoSiteBundle:Page:documentation.html.twig', array(
      'menu' => $this->getPageMenu(),
    ));
  }

  public function resourceAction($hash)
  {
    $repo = $this->getDoctrine()
      ->getRepository('TornadoApiBundle:Resource');

    $resource = $repo->findOneBy(
      array('hash' => $hash)
    );

    // $query = $repo->createQueryBuilder('r')->getQuery();
    // $resource = $query->getResult();

    return $this->render('TornadoSiteBundle:Page:share.html.twig', array(
      'menu' => $this->getPageMenu(),
      'Resource' => $resource,
      'File' => file_get_contents($resource->getAbsolutePath()),
    ));
  }
}
