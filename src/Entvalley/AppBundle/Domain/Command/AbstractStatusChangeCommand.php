<?php

namespace Entvalley\AppBundle\Domain\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Entvalley\AppBundle\Domain\UserContext;
use Entvalley\AppBundle\Entity\Comment;
use Entvalley\AppBundle\Entity\Task;

abstract class AbstractStatusChangeCommand extends AbstractCommand
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;

    /**
     * @var UserContext
     */
    protected $userContext;

    /**
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     * @param UserContext $userContext
     */
    public function __construct(Registry $doctrine, UserContext $userContext)
    {
        $this->doctrine = $doctrine;
        $this->userContext = $userContext;
    }

    abstract public function getNewStatus();

    public function execute($content)
    {
        $em = $this->doctrine->getManager();

        /** @var $task Task */
        $task = $em->find('EntvalleyAppBundle:Task', $updatedId = $this->source->getContextId());
        if (!$task) {
            return [];
        }

        $task->setStatus($this->userContext->getUser(), $this->getNewStatus());
        $this->onStatusChange($task);

        $comment = new Comment;
        $comment->setText($content);
        $comment->setAuthor($this->userContext->getUser());
        $comment->setTask($task);
        $status = $task->getLastStatus();
        $comment->setStatusChange($status);

        $em->persist($status);
        $em->persist($comment);

        return [
            'comment' => $comment,
            'updatedId' => (int)$updatedId,
        ];
    }

    public function onStatusChange($task)
    {
        return $task;
    }
}