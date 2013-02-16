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

    protected $htmlPurifier;

    /**
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     * @param UserContext $userContext
     */
    public function __construct(Registry $doctrine, UserContext $userContext, $htmlPurifier)
    {
        $this->doctrine = $doctrine;
        $this->userContext = $userContext;
        $this->htmlPurifier = $htmlPurifier;
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
        $comment->setHtmlPurifier($this->htmlPurifier);
        $status = $task->getLastStatus();
        $comment->setStatusChange($status);

        $em->persist($status);
        $em->persist($comment);

        return [
            'status' => $this->getNewStatus(),
            'comment' => $comment,
            'updatedId' => (int)$updatedId,
        ];
    }

    public function onStatusChange($task)
    {
        return $task;
    }

    public function getApplicableInText()
    {
        return 'a task';
    }
}