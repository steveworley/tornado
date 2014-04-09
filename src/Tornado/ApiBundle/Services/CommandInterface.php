<?php

namespace Tornado\ApiBundle\Services;
use Symfony\Component\Filesystem\Filesystem;

abstract class CommandInterface
{
  /**
   * @var FileSystem
   */
  private $fileSystem;

  /**
   * @var string
   */
  private $file;

  /**
   * @var string
   */
  private $command;

  /**
   * @var type
   */
  private $type;

  /**
   * Build a CommandInterface class. These params should be injected with
   * dependency injection.
   */
  public function __construct(FileSystem $fileSystem, $command)
  {
    $this->setFileSystem($fileSystem);
    $this->setCommand($command);
  }

  /**
   * Set a \file
   *
   * @param string
   * @return CommandInterface
   */
  public function setFile($file)
  {
    $this->file = $file;
    return $this;
  }

  /**
   * Get \file
   *
   * @return string
   */
  protected function getFile()
  {
    return $this->file;
  }

  /**
   * Set \command
   *
   * @param string
   * @return CommandInterface
   */
  protected function setCommand($command)
  {
    $this->command = $command;
    return $this;
  }

  /**
   * Get \command
   *
   * @return string.
   */
  protected function getCommand()
  {
    return $this->command;
  }

  /**
   * Set \Filesystem.
   *
   * @param FileSystem.
   * @return CommandInterface
   */
  protected function setFileSystem(FileSystem $fileSystem)
  {
    $this->fileSystem = $fileSystem;
    return $this;
  }

  /**
   * Get \FileSystem
   *
   * @return FileSystem
   */
  protected function getFileSystem()
  {
    return $this->fileSystem;
  }

  /**
   * Set \type
   *
   * @param string
   * @return CommandInterface
   */
  protected function setType($type)
  {
    $this->type = $type;
    return $this;
  }

  /**
   * Get \type
   *
   * @return string
   */
  protected function getType()
  {
    return $this->type;
  }

  /**
   * Run the command over a given resource.
   *
   * This function is passed a Resource object, the resource object will be
   * modified depedning on which commands are currently available. This will
   * add these values to the Resource prior to the resource being inserted into
   * the database.
   *
   * @param Resource $resource
   * @return mixed
   */
  public function run($resource)
  {
    $output = null;

    if (!$this->getFileSystem()->exists($this->getFile())) {
      // If this file hasn't been set we should terminate early.
      throw new \Exception("Cannot locate file");
    }

    // Begin the command phase - this will execute this classes mmethod.
    $handle = popen($this->getCommand() . " " . $this->getFile(), 'r');

    while (!feof($handle)) {
      $output .= fread($handle, 1024);
    }

    if (null === $output) {
      throw new \Exception("No output recieved");
    }

    $output = $this->formatOutput($output);
    $resource->set($this->getType(), $output);

    return $output;
  }

  /**
   * Define a base function for how these classes will deal the output from
   * the shell command that is executed during the run() method.
   *
   * @type Abstract
   */
  abstract function formatOutput($output);
}
