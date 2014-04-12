<?php
/**
 * @file
 * Define a structure for a user object.
 */
namespace Tornado\UserBundle\Models;

class User {
  /**
   * @var protected
   */
  string $name;

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
}
