<?php

namespace Entvalley\AppBundle\Tests\Domain\Command;

use Entvalley\AppBundle\Domain\Command\CommandRegistry;

class CommandRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldReturnCommandByName()
    {
        $registry = new CommandRegistry();
        $registry->register($close = new CloseCommandStub());
        $registry->register($create = new CreateCommandStub());

        $this->assertEquals($create, $registry->get('create'));
        $this->assertEquals($close, $registry->get('close'));
    }

    public function testShouldReturnRegisteredCommandsNames()
    {
        $registry = new CommandRegistry();
        $registry->register($close = new CloseCommandStub());
        $registry->register($create = new CreateCommandStub());

        $this->assertEquals(array('close', 'create'), $registry->getRegisteredNames());
    }
}