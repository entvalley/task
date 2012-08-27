<?php

namespace Entvalley\AppBundle\Domain\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Entvalley\AppBundle\Entity\User;
use Entvalley\AppBundle\Entity\Task;
use Entvalley\AppBundle\Domain\Status;

class DoneCommand extends AbstractCommand
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;
    private $user;

    public function __construct(Registry $doctrine, User $user)
    {
        $this->doctrine = $doctrine;
        $this->user = $user;
    }

    public function execute($content)
    {
        $em = $this->doctrine->getManager();
        $task = $em->find('EntvalleyAppBundle:Task', $updatedId = $this->source->getContextId());
        if (!$task) {
            return array();
        }

        $task->setStatus($this->user, Status::CLOSED);

        return array('updatedId' => (int)$updatedId);
    }

    public function getName()
    {
        return 'done';
    }
}