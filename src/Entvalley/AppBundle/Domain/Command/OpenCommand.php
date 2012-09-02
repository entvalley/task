<?php

namespace Entvalley\AppBundle\Domain\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Entvalley\AppBundle\Entity\User;
use Entvalley\AppBundle\Domain\Status;
use Entvalley\AppBundle\Entity\Task;

class OpenCommand extends AbstractCommand
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    /**
     * @var User
     */
    private $user;

    /**
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     * @param User $user
     */
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

        $task->setStatus($this->user, Status::REOPENED);

        return array('updatedId' => (int)$updatedId);
    }

    public function getName()
    {
        return 'open';
    }
}