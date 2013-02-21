<?php

namespace Entvalley\AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class CommandPass implements custom pass that:
 *   1: Registers every command with the command tag in the registry
 *
 * @package Entvalley\AppBundle\DependencyInjection\Compiler
 */
class CommandPass implements CompilerPassInterface
{
    const COMMAND_TAG = 'entvalley.command';

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('entvalley.command_registry')) {
            return;
        }

        $commandRegistryDefinition = $container->getDefinition('entvalley.command_registry');

        foreach ($container->findTaggedServiceIds(self::COMMAND_TAG) as $id => $attributes) {
            $commandRegistryDefinition->addMethodCall('register', array(new Reference($id)));
        }
    }
}