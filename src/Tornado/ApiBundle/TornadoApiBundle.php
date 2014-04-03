<?php

namespace Tornado\ApiBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Tornado\ApiBundle\DependencyInjection\Compiler\CommandCompilerPass;

class TornadoApiBundle extends Bundle
{
  public function build(ContainerBuilder $container)
  {
    parent::build($container);
    $container->addCompilerPass(new CommandCompilerPass);
  }
}
