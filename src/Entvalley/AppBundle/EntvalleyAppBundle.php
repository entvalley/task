<?php

namespace Entvalley\AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Entvalley\AppBundle\DependencyInjection\Compiler\CommandPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EntvalleyAppBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CommandPass());
    }
}
