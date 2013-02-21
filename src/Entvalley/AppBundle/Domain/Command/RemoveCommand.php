<?php

namespace Entvalley\AppBundle\Domain\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RemoveCommand extends AbstractCommand
{
    /**
     * @var \Symfony\Bridge\Doctrine\RegistryInterface
     */
    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function execute($content)
    {
        $em = $this->doctrine->getManager();
        $task = $em->find('EntvalleyAppBundle:Task', $removedId = $this->source->getContextId());
        if (!$task) {
            return [];
        }

        $em->remove($task);

        return ['removed_id' => (int)$removedId];
    }

    public function getName()
    {
        return 'remove';
    }
}