<?php

namespace Entvalley\AppBundle\Domain\Command;

use Entvalley\AppBundle\Domain\Status;

class RejectCommand extends AbstractStatusChangeCommand
{
    public function getNewStatus()
    {
        return Status::REJECTED;
    }

    public function getName()
    {
        return 'reject';
    }
}