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

    public function isSatisfiedBySource(CommandSource $source)
    {
        return true;
    }

    public function getApplicableInText()
    {
        return 'a task';
    }
}
