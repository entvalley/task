<?php

namespace Entvalley\AppBundle\Domain\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Entvalley\AppBundle\Domain\UserContext;
use Entvalley\AppBundle\Entity\Task;

class CreateCommand extends AbstractCommand
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    private $userContext;

    public function __construct(Registry $doctrine, UserContext $userContext)
    {
        $this->doctrine = $doctrine;
        $this->userContext = $userContext;
    }

    public function execute($content)
    {
        $em = $this->doctrine->getManager();

        $task = new Task;
        $task->setCreatedAt(new \DateTime());
        $task->setAuthor($this->userContext->getUser());
        $task->setTextWithTitle($content);
        $task->setCompany($this->userContext->getUser()->getCompany());

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