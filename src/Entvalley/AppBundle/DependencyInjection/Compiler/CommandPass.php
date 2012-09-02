<?php

namespace Entvalley\AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CommandPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('entvalley.command_registry')) {
            return;
        }

        $commandRegistryDefinition = $container->getDefinition('entvalley.command_registry');

        foreach ($container->findTaggedServiceIds('entvalley.command') as $id => $attributes) {
            $commandRegistryDefinition->addMethodCall('register', array(new Reference($id)));
        }
    }
}