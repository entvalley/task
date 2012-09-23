<?php

namespace Entvalley\AppBundle\Domain\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Entvalley\AppBundle\Entity\User;
use Entvalley\AppBundle\Domain\Status;
use Entvalley\AppBundle\Entity\Task;

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