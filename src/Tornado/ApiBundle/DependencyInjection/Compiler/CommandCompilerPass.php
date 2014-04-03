<?php
namespace Tornado\ApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CommandCompilerPass implements CompilerPassInterface
{
  public function process(ContainerBuilder $container)
  {
    if (!$container->hasDefinition('tornado_api.command_bag')) {
      return;
    }

    $definition = $container->getDefinition('tornado_api.command_bag');
    $taggedServices = $container->findTaggedServiceIds('tornado_api.command');

    foreach ($taggedServices as $id => $attributes) {
      $definition->addMethodCall(
        'addCommand',
        array(new Reference($id))
      );
    }
  }
}
