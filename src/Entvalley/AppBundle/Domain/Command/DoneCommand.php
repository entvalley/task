<?php

namespace Entvalley\AppBundle\Domain\Command;

use Entvalley\AppBundle\Domain\Status;

class DoneCommand extends AbstractStatusChangeCommand
{
    public function getNewStatus()
    {
        return Status::CLOSED;
    }

    public function getName()
    {
        return 'done';
    }
}