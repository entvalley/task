<?php

namespace Entvalley\AppBundle\Tests\Domain\Command;

use Entvalley\AppBundle\Domain\Command\AbstractCommand;
use Entvalley\AppBundle\Domain\Command\CommandSource;

class CreateCommandStub extends AbstractCommand
{
    public function execute($content)
    {
        return true;
    }

    public function getName()
    {
        return 'create';
    }

    public function isGuessableBySource(CommandSource $source)
    {
        return true;
    }
}
