<?php

namespace Tornado\ApiBundle\Services;
use Tornado\ApiBundle\Services\CommandInterface;

class MessDetector extends CommandInterface
{
  public function __construct($filesystem, $command)
  {
    parent::__construct($filesystem, $command);
    $this->setType('messdetector');
  }

  public function formatOutput($output)
  {

  }
}
