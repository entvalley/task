<?php

namespace Entvalley\AppBundle\Domain\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Entvalley\AppBundle\Entity\Comment;
use Entvalley\AppBundle\Entity\User;
use Entvalley\AppBundle\Domain\Status;
use Entvalley\AppBundle\Entity\Task;

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