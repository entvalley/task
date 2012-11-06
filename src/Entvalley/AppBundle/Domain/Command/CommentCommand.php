<?php

namespace Entvalley\AppBundle\Domain\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Entvalley\AppBundle\Domain\UserContext;
use Entvalley\AppBundle\Entity\Comment;

class CommentCommand extends AbstractCommand
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;
    private $userContext;

    protected $isVisible = false;


    public function __construct(Registry $doctrine, UserContext $userContext)
    {
        $this->doctrine = $doctrine;
        $this->userContext = $userContext;
    }

    public function execute($content)
    {
        $em = $this->doctrine->getManager();

        $task = $em->find('EntvalleyAppBundle:Task', $this->source->getContextId());

        if (!$task) {
            return;
        }

        $comment = new Comment;
        $comment->setText($content);
        $comment->setCreatedAt(new \DateTime());
        $comment->setAuthor($this->userContext->getUser());
        $comment->setTask($task);

        $em->persist($comment);

        return [
            'comment' => $comment
            ];
    }

    public function getName()
    {
        return 'comment';
    }

    public function isGuessableBySource(CommandSource $source)
    {
        return ($source->getContextType() == 'task' && $source->getContextId() > 0);
    }
}