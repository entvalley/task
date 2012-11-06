<?php

namespace Entvalley\AppBundle\Domain\Command;

use Entvalley\AppBundle\Domain\Status;

class TakeCommand extends AbstractStatusChangeCommand
{
    public function getNewStatus()
    {
        return Status::ACCEPTED;
    }

    public function onStatusChange($task)
    {
        $task->setAssignedTo($this->userContext->getUser());
    }

    public function getName()
    {
        return 'take';
    }
}