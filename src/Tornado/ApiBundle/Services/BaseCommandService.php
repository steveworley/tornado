<?php

namespace Tornado\ApiBundle\Services;
use Symfony\Component\Filesystem\Filesystem;

abstract class BaseCommandService
{
  private $fileSystem;
  private $file;
  private $command;
  private $type;

  public function __construct(FileSystem $fileSystem, $command, $type)
  {
    $this->setFileSystem($fileSystem);
    $this->setCommand($command);
    $this->setType($type);
  }

  public function setFile($file)
  {
    $this->file = $file;
    return $this;
  }

  protected function getFile()
  {
    return $this->file;
  }

  protected function setCommand($command)
  {
    $this->command = $command;
    return $this;
  }

  protected function getCommand()
  {
    return $this->command;
  }

  protected function setFileSystem(FileSystem $fileSystem)
  {
    $this->fileSystem = $fileSystem;
    return $this;
  }

  protected function getFileSystem()
  {
    return $this->fileSystem;
  }

  protected function setType($type)
  {
    $this->type = $type;
    return $this;
  }

  protected function getType()
  {
    return $this->type;
  }

  public function run($resource)
  {
    $output = null;

    if (!$this->getFileSystem()->exists($this->getFile())) {
      throw new \Exception("Cannot locate file");
    }

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

  abstract function formatOutput($output);
}
