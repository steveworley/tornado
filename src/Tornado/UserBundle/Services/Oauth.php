<?php

namespace Tornado\UserBundle\Services;

use Symfony\Component\HttpFoundation\Session\Session;
use Guzzle\Http\Client;

class Oauth {

  /**
   * Configuration values for this oAuth source.
   *
   * @var\array
   */
  private $config;

  /**
   * @var Symfony\Component\HttpFoundation\Session\Session
   */
  private $session;

  /**
   * @var Guzzle\Http\Client
   */
  private $client;

  /**
   * Construct the oAuth class.
   */
  public function __construct(Session $session, $config)
  {
    $this->set('session', $session);
    $this->set('config', $config);
    $this->set('client', new Client);
  }

  /**
   * Getter for config.
   *
   * @return array
   */
  public function getConfig()
  {
    return $this->config;
  }

  /**
   * Setter for config.
   *
   * @param array
   * @return \Oauth
   */
  public function setConfig($config)
  {
    $this->config = $config;

    return $this;
  }

  /**
   * Getter for client.
   *
   * @return Guzzle\Http\Client
   */
  public function getClient()
  {
    return $this->client;
  }

  /**
   * Setter for client.
   *
   * @param Guzzle\Http\Client
   * @return \Oauth
   */
  public function setClient(Client $client)
  {
    $this->client = $client;

    return $this;
  }

  /**
   * Getter for session.
   *
   * @return Symfony\Component\HttpFoundation\Session\Session
   */
  public function getSession()
  {
    return $this->session;
  }

  /**
   * Setter for session.
   *
   * @param Symfony\Component\HttpFoundation\Session\Session
   * @return \Oauth
   */
  public function setSession($session)
  {
    $this->session = $session;

    return $this;
  }

  /**
   * Access any defined property.
   *
   * @param string $property
   *   A property that is on this service.
   *
   * @return mixed
   *   $this->{$property}
   */
  public function get($property)
  {
    $getter = array($this,"get" . ucfirst($property));

    if (is_callable($getter)) {
      return call_user_func($getter);
    }

    $config = $this->getConfig();
    return empty($config[$property]) ? FALSE : $config[$property];
  }

  /**
   * Set any defined property.
   *
   * @param string $property
   *   Any property defined on this service.
   * @param mixed $value
   *   A value to assign to $property.
   */
  public function set($property, $value)
  {
    $setter = array($this, "set" . ucfirst($property));

    if (is_callable($setter)) {
      return call_user_func($setter, $value);
    }

    return FALSE;
  }

  /**
   * Handle the response from the oAuth source.
   *
   * This method should take the post fields that are returned with the
   * response from the oAuth and then build the correct URL to request
   * an access token for two factor auth sources.
   */
  public function handleResponse($request) {}

  /**
   * Get an access token from the oAuth source.
   *
   * After the respose from the oAuth source has been processed and determined
   * that an access token can be accessed- this method should interact with the
   * providers API to attain an access token for the user.
   */
  public function getAccessToken($request) {}

  /**
   * Send another request to complete to 2fa.
   *
   * Given the $access_token form a previous request. We can access the users
   * data by making another request and completing the two factor auth. This
   * method should be overriden in each oAuth class.
   */
  public function getUserData($access_token) {}
}
