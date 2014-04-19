<?php
/**
 * @file
 * Define a structure for a user object.
 */
namespace Tornado\UserBundle\Models;

class User {

  /**
   * @var integer
   */
  protected $id;

  /**
   * @var string
   */
  protected $username;

  /**
   * @var string
   */
  protected $name;

  /**
   * @var string
   */
  protected $email;

  /**
   * @var array
   */
  protected $projects;

  /**
   * @var array
   */
  protected $tokens;

  /**
   * Getter for \id.
   *
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Getter for \username.
   *
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }

  /**
   * Setter for \username.
   *
   * @param string $username
   * @return \User
   */
  public function setUsername($username)
  {
    $this->username = $username;
    return $this;
  }

  /**
   * Getter for \name.
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Setter for \name.
   *
   * @param string $name
   * @return \User
   */
  public function setName($name)
  {
    $this->name = $name;
    return $this;
  }

  /**
   * Getter for \email.
   *
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * Setter for \email.
   *
   * @param string $email
   * @return \User
   */
  public function setEmail($email)
  {
    $this->email = $email;
    return $this;
  }

  /**
   * Getter for \projects.
   *
   * @return string
   */
  public function getProjects()
  {
    return $this->projects;
  }

  /**
   * Setter for \projects.
   *
   * @param string $projects
   * @return \User
   */
  public function setProjects($projects) {
    $this->projects = $projects;
    return $this;
  }

  /**
   * Getter for \tokens.
   *
   * @return string
   */
  public function getTokens()
  {
    return $this->tokens;
  }

  /**
   * Setter for \tokens.
   *
   * @param string $tokens
   * @return \User
   */
  public function setTokens($tokens) {
    $this->tokens = $tokens;
    return $this;
  }

  /**
   * Get specific service token.
   *
   * @param  string $service
   *   The name of an available service.
   *
   * @return string
   *   The token for $service.
   */
  public function getServiceToken($service)
  {
    if (isset($this->getTokens()[$service])) {
      return $this->getTokens()[$service];
    }

    return FALSE;
  }
}
