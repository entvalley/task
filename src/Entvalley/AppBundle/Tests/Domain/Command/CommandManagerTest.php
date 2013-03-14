<?php

namespace Entvalley\AppBundle\Tests\Domain\Command;

use Entvalley\AppBundle\Domain\Command\CommandManager;

class CommandManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldReturnCommandNamesWithAtSign()
    {
        $registryMock = $this->getMock('\Entvalley\AppBundle\Domain\Command\CommandRegistry');
        $registryMock->expects($this->once())
            ->method('getRegisteredNames')
            ->will($this->returnValue(array(
            'create',
            'close'
        )));

        $userContextMock = $this->getMock('Entvalley\AppBundle\Domain\UserContextInterface');
        $statsService = $this->getMock('Entvalley\AppBundle\Service\StatsServiceInterface');
        $interpreterMock = $this->getMock('\Entvalley\AppBundle\Domain\Command\CommandInterpreter', array(), array($registryMock, $userContextMock, $statsService));

        $manager = new CommandManager($interpreterMock, $registryMock);

        $this->assertEquals(array('@create', '@close'), $manager->getCommandNames());
    }

    public function testShouldReturnCommandsWithTheirConfiguration()
    {
        $close = new CloseCommandStub();
        $create = new CreateCommandStub();
        $registryMock = $this->getMock('\Entvalley\AppBundle\Domain\Command\CommandRegistry');
        $registryMock->expects($this->once())
            ->method('getAll')
            ->will($this->returnValue(array(
            $create,
            $close
        )));

        $userContextMock = $this->getMock('Entvalley\AppBundle\Domain\UserContextInterface');
        $statsService = $this->getMock('Entvalley\AppBundle\Service\StatsServiceInterface');
        $interpreterMock = $this->getMock('Entvalley\AppBundle\Domain\Command\CommandInterpreter', array(), array($registryMock, $userContextMock, $statsService));


        $manager = new CommandManager($interpreterMock, $registryMock);

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
        $registryMock = $this->getMock('\Entvalley\AppBundle\Domain\Command\CommandRegistry');

        $registryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('close'))
            ->will($this->returnValue(
            $close
        ));

        $userContextMock = $this->getMock('Entvalley\AppBundle\Domain\UserContextInterface');
        $statsService = $this->getMock('Entvalley\AppBundle\Service\StatsServiceInterface');
        $interpreterMock = $this->getMock('Entvalley\AppBundle\Domain\Command\CommandInterpreter', array(), array($registryMock, $userContextMock, $statsService));


        $manager = new CommandManager($interpreterMock, $registryMock);

        $this->assertEquals([
            'is_visible' => true,
            'applicable_in' => 'a task'
        ], $manager->getCommandConfig('close'));
    }
}