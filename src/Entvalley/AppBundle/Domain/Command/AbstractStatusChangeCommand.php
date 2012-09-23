<?php

namespace Entvalley\AppBundle\Domain\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Entvalley\AppBundle\Entity\Comment;
use Entvalley\AppBundle\Entity\User;
use Entvalley\AppBundle\Domain\Status;
use Entvalley\AppBundle\Entity\Task;

abstract class AbstractStatusChangeCommand extends AbstractCommand
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;

    /**
     * @var User
     */
    protected $user;

    /**
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     * @param User $user
     */
    public function __construct(Registry $doctrine, User $user)
    {
        $this->doctrine = $doctrine;
        $this->user = $user;
    }

    abstract public function getNewStatus();

    public function execute($content)
    {
        $em = $this->doctrine->getManager();
        $task = $em->find('EntvalleyAppBundle:Task', $updatedId = $this->source->getContextId());
        if (!$task) {
            return array();
        }

        $task->setStatus($this->user, $this->getNewStatus());
        $this->onStatusChange($task);

        $comment = new Comment;
        $comment->setText($content);
        $comment->setAuthor($this->user);
        $comment->setTask($task);
        $status = $task->getLastStatus();
        $comment->setStatusChange($status);

        $em->persist($status);
        $em->persist($comment);

        return array(
            'comment' => $comment,
            'updatedId' => (int)$updatedId,
        );
    }

    public function onStatusChange($task)
    {
        return $task;
    }
}