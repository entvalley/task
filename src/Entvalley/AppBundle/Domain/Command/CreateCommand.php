<?php

namespace Entvalley\AppBundle\Domain\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Entvalley\AppBundle\Entity\Task;

class CreateCommand extends AbstractCommand
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    private $user;

    public function __construct(Registry $doctrine, $user)
    {
        $this->doctrine = $doctrine;
        $this->user = $user;
    }

    public function execute($content)
    {
        $em = $this->doctrine->getManager();

        $task = new Task;
        $task->setCreatedAt(new \DateTime());
        $task->setAuthor($this->user);
        $task->setTextWithTitle($content);
        $task->setCompany($this->user->getCompany());

        $em->persist($task);

        return array(
            'task' => $task
            );
    }

    public function getName()
    {
        return 'create';
    }

    public function isGuessableBySource(CommandSource $source)
    {
        return ($source->getContextType() == 'tasks' && $source->getContextId() == '');
    }
}