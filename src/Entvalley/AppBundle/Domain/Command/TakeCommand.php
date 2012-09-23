<?php

namespace Entvalley\AppBundle\Domain\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Entvalley\AppBundle\Domain\Status;
use Entvalley\AppBundle\Entity\User;
use Entvalley\AppBundle\Entity\Task;

class TakeCommand extends AbstractStatusChangeCommand
{
    public function getNewStatus()
    {
        return Status::ACCEPTED;
    }

    public function onStatusChange($task)
    {
        $task->setAssignedTo($this->user);
    }

    public function getName()
    {
        return 'take';
    }
}