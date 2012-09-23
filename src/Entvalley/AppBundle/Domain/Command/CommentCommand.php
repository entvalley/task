<?php

namespace Entvalley\AppBundle\Domain\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Entvalley\AppBundle\Entity\Comment;

class CommentCommand extends AbstractCommand
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;
    private $user;

    protected $isVisible = false;


    public function __construct(Registry $doctrine, $user)
    {
        $this->doctrine = $doctrine;
        $this->user = $user;
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
        $comment->setAuthor($this->user);
        $comment->setTask($task);

        $em->persist($comment);

        return array(
            'comment' => $comment
            );
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