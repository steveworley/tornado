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
  private $commands;

  /**
   * Public constructor for the CommandBag.
   * Ensures that $this->command is instantiated as an array.
   */
  public function __construct()
  {
    $this->commands = array();
  }

  /**
   * Add \Command
   *
   * @param CommandBase $command [description]
   */
  public function addCommand(CommandInterface $command)
  {
    $this->commands[] = $command;
    return $this;
  }

  public function getCommands()
  {
    return $this->commands;
  }

  public function getCommand($alias)
  {
    if (array_key_exists($alias, $this->commands)) {
      return $this->commands[$alias];
    }
  }
}
