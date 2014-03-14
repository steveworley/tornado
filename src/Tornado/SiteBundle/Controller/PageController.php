<?php

// src/Tornado/SiteBundle/Controller/PageController.php
namespace Tornado\SiteBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

  public function indexAction()
  {
    exec("php ~/Scripts/phploc.phar /tmp/test.php > /tmp/phploc.output");

    $content = file_get_contents("/tmp/phploc.output");
    $code = file_get_contents("/tmp/test.php");

    $string = str_replace('phploc 2.0.4 by Sebastian Bergmann.', '', $content);
    $prev_item = NULL;
    $output = array();
    foreach (explode("\n", $string) as $item) {
      if (!preg_match("/^(\s)/", $item)) {
        $prev_item = trim($item);
      }
      else {
        $value = trim($item);
        $value = preg_split("/\s\s([\s]+)?/", $value);
        if (count($value) == 2) {
          $output[$prev_item][$value[0]] = $value[1];
        }
      }
    }

    $resource = new Resource();
    $resource->setHash(md5('test.php'));
    $resource->setOutput($output);
    $resource->setCreated(time());

    $tab_headers = array();
    $tab_content = array();
    foreach ($output as $key => $value) {
      $tab_headers[] = $key;
      $tab_content[] = $value;
    }

    return $this->render('TornadoSiteBundle:Page:index.html.twig', array(
      'menu' => $this->getPageMenu(),
      'breakdown' => $resource->getOutput(),
      'code' => $code,
      'tab' => array(
        'headers' => $tab_headers,
        'content' => $tab_content,
      ),
    ));
  }

  public function documentationAction()
  {
    return $this->render('TornadoSiteBundle:Page:documentation.html.twig', array(
      'menu' => $this->getPageMenu(),
    ));
  }
}
