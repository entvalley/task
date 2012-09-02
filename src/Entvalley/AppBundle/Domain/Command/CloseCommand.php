<?php

namespace Entvalley\AppBundle\Domain\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Entvalley\AppBundle\Entity\Task;

class CloseCommand extends DoneCommand
{
    public function getName()
    {
        return 'close';
    }
}