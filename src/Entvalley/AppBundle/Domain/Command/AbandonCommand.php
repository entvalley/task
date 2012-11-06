<?php

namespace Entvalley\AppBundle\Domain\Command;

use Entvalley\AppBundle\Domain\Status;

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