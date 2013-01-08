<?php

namespace Entvalley\AppBundle\Tests\Domain\Command;

use Entvalley\AppBundle\Domain\Command\CommandManager;

class CommandManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldReturnCommandNamesWithAtSign()
    {
        $regsitryMock = $this->getMock('\Entvalley\AppBundle\Domain\Command\CommandRegistry');
        $regsitryMock->expects($this->once())
            ->method('getRegisteredNames')
            ->will($this->returnValue(array(
            'create',
            'close'
        )));


        $interpreterMock = $this->getMock('\Entvalley\AppBundle\Domain\Command\CommandInterpreter', array(), array($regsitryMock));

        $manager = new CommandManager($interpreterMock, $regsitryMock);

        $this->assertEquals(array('@create', '@close'), $manager->getCommandNames());
    }

    public function testShouldReturnCommandsWithTheirConfiguration()
    {
        $close = new CloseCommandStub();
        $create = new CreateCommandStub();
        $regsitryMock = $this->getMock('\Entvalley\AppBundle\Domain\Command\CommandRegistry');
        $regsitryMock->expects($this->once())
            ->method('getAll')
            ->will($this->returnValue(array(
            $create,
            $close
        )));

        $interpreterMock = $this->getMock('\Entvalley\AppBundle\Domain\Command\CommandInterpreter', array(), array($regsitryMock));

        $manager = new CommandManager($interpreterMock, $regsitryMock);

        $this->assertEquals(array('@create' => array(
            'is_visible' => true,
            'applicable_in' => 'a task'
        ), '@close' => array(
            'is_visible' => true,
            'applicable_in' => 'a task'
        )), $manager->getCommandsConfigs());
    }

    public function testShouldReturnConfigForSpecifiedCommandOnly()
    {
        $close = new CloseCommandStub();
        $create = new CreateCommandStub();
        $regsitryMock = $this->getMock('\Entvalley\AppBundle\Domain\Command\CommandRegistry');
        $regsitryMock->expects($this->once())
            ->method('getAll')
            ->will($this->returnValue(array(
            $create,
            $close
        )));

        $interpreterMock = $this->getMock('\Entvalley\AppBundle\Domain\Command\CommandInterpreter', array(), array($regsitryMock));

        $manager = new CommandManager($interpreterMock, $regsitryMock);

        $this->assertEquals([
            'is_visible' => true,
            'applicable_in' => 'a task'
        ], $manager->getCommandConfig('close'));
    }
}