<?php

namespace Entvalley\AppBundle\Domain\Command;

use Entvalley\AppBundle\Domain\Status;

class OpenCommand extends AbstractStatusChangeCommand
{
    public function getNewStatus()
    {
        return Status::REOPENED;
    }

    public function getName()
    {
        return 'open';
    }
}