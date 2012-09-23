<?php

namespace Entvalley\AppBundle\Domain\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Entvalley\AppBundle\Entity\Comment;
use Entvalley\AppBundle\Entity\User;
use Entvalley\AppBundle\Entity\Task;
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