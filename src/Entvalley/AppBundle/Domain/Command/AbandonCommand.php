<?php

namespace Entvalley\AppBundle\Domain\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Entvalley\AppBundle\Entity\User;
use Entvalley\AppBundle\Domain\Status;
use Entvalley\AppBundle\Entity\Task;

class AbandonCommand extends AbstractStatusChangeCommand
{
    public function getNewStatus()
    {
        return Status::UNASSIGNED;
    }

    public function onStatusChange($task)
    {
        $task->setAssignedTo(null);
    }

    public function getName()
    {
        return 'abandon';
    }
}