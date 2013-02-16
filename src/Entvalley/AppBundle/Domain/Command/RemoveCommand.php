<?php

namespace Entvalley\AppBundle\Domain\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;

class RemoveCommand extends AbstractCommand
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    public function __construct(Registry $doctrine)
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