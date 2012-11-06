<?php

namespace Entvalley\AppBundle\Domain\Command;

class CloseCommand extends DoneCommand
{
    public function getName()
    {
        return 'close';
    }
}