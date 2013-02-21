<?php

namespace Entvalley\AppBundle\Tests\Domain\Command;

use Entvalley\AppBundle\Domain\Command\Command;
use Entvalley\AppBundle\Domain\Command\RemoveCommand;
use MyProject\Proxies\__CG__\stdClass;

class RemoveCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testRemovesTask()
    {
        $command = $this->createRemoveCommand($this->getObjectManager());

        $command->execute('no content');
    }

    /**
     * @return Command
     */
    private function createRemoveCommand($objectManagerMock)
    {
        $registryMock = $this->getMock('Symfony\Bridge\Doctrine\RegistryInterface');

        $registryMock->expects($this->any())
            ->method('getManager')
            ->will($this->returnValue($objectManagerMock));

        $command = new RemoveCommand($registryMock);

        $sourceMock = $this->getMock('Entvalley\AppBundle\Domain\Command\CommandSource');

        $sourceMock->expects($this->once())
            ->method('getContextId')
            ->will($this->returnValue(1));

        $command->setSource($sourceMock);
        return $command;
    }

    private function getObjectManager()
    {
        $objectManagerMock = $this->getMock('Doctrine\Common\Persistence\ObjectManager');

        $objectManagerMock->expects($this->once())
            ->method('find')
            ->will($this->returnValue(new stdClass()));

        $objectManagerMock->expects($this->once())
            ->method('remove');
    }

}