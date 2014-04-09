<?php
/**
 * CommandBag class.
 *
 * The command chain is a container for all services tagged with
 * "tornado_api.command". Each command in the chain will be executed during
 * the creation of a Resource and will have their output bundle with the
 * resource.
 */
namespace Tornado\ApiBundle\Services;

class CommandBag
{
  /**
   * @var array
   */
  private $commands;

  /**
   * Public constructor for the CommandBag.
   * Ensures that $this->commands is instantiated as an array.
   */
  public function __construct()
  {
    $this->commands = array();
  }

  /**
   * Add \Command
   *
   * @param CommandInterface
   * @return CommandBag
   */
  public function addCommand(CommandInterface $command)
  {
    $this->commands[] = $command;
    return $this;
  }

  /**
   * Get \Commands
   *
   * @return array
   */
  public function getCommands()
  {
    return $this->commands;
  }
}
