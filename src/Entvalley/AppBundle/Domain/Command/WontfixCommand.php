<?php

namespace Entvalley\AppBundle\Domain\Command;

use Entvalley\AppBundle\Domain\Status;

class WontfixCommand extends AbstractStatusChangeCommand
{
    public function getNewStatus()
    {
        return Status::WONTFIX;
    }

    public function getName()
    {
        return 'wontfix';
    }
}