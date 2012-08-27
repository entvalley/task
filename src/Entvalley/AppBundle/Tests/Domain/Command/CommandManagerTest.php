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
}