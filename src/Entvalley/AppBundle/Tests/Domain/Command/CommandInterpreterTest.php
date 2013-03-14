<?php

namespace Entvalley\AppBundle\Tests\Domain\Command;

use Entvalley\AppBundle\Domain\Command\CommandInterpreter;
use Entvalley\AppBundle\Domain\Command\CommandRegistry;
use Entvalley\AppBundle\Domain\Command\CommandSource;

class CommandInterpreterTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldExtractCommandFromSource()
    {
        $interpreter = $this->createInterpreterThatExpects("a new ticket");
        $source = "@create a new ticket";

        $result = $interpreter->interpret(new CommandSource($source));
        $this->assertEquals(array('create' => array(true)), $result);
    }

    public function testShouldAcceptCommandWithoutContent()
    {
        $interpreter = $this->createInterpreterThatExpects('');
        $source = "@create";

        $result = $interpreter->interpret(new CommandSource($source));
        $this->assertEquals(array('create' => array(true)), $result);
    }

    public function testShouldExecuteTwoCommandsWithTheSameName()
    {
        $registry = new CommandRegistry();
        $expectedCreateCommand = $this->getMock('Entvalley\AppBundle\Tests\Domain\Command\CreateCommandStub');
        $expectedCreateCommand->expects($this->atLeastOnce())
            ->method('execute')
            ->will($this->returnValue(true));
        $expectedCreateCommand->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('create'));
        $registry->register($expectedCreateCommand);

        $userContextMock = $this->getMock('Entvalley\AppBundle\Domain\UserContextInterface');
        $statsService = $this->getMock('Entvalley\AppBundle\Service\StatsServiceInterface');

        $interpreter = new CommandInterpreter($registry, $userContextMock, $statsService);

        $source = "@create a new ticket\r\n";
        $source .= "@create another ticket";

        $result = $interpreter->interpret(new CommandSource($source));
        $this->assertEquals(array('create' => array(true, true)), $result);
    }

    public function testShouldExtractTwoCommandsFromSource()
    {
        $interpreter = $this->createInterpreterThatExpects("a \r\n new ticket", "this ticket");

        $source = "@create a \r\n new ticket\r\n";
        $source .= "@close this ticket";

        $result = $interpreter->interpret(new CommandSource($source));

        $this->assertEquals(array('create' => array(true), 'close' =>  array(true)), $result);
    }

    public function testShouldNotCreateCommandIfItDoesNotStartWithNewLine()
    {
        $interpreter = $this->createInterpreterThatExpects("you can use @create to create tickets");
        $source = "@create you can use @create to create tickets";

        $result = $interpreter->interpret(new CommandSource($source));

        $this->assertEquals(array('create' => array(true)), $result);
    }

    public function testShouldNotCreateUnknownCommand()
    {
        $interpreter = $this->createInterpreterThatExpects("i think we should add \r\n@unknown command");

        $source = "@create i think we should add \r\n";
        $source .= "@unknown command";

        $result = $interpreter->interpret(new CommandSource($source));

        $this->assertEquals(array('create' => array(true)), $result);
    }


    public function testShouldGuessCommandFromSourceAndParseNamedCommand()
    {
        $interpreter = $this->createInterpreterThatExpects("we should close this ticket", "");

        $source = "we should close this ticket \r\n";
        $source .= "@close";

        $result = $interpreter->interpret(new CommandSource($source));

        $this->assertEquals(array('create' => array(true), 'close' =>  array(true)), $result);
    }

    public function testShouldCountEveryCommandExecution()
    {
        $registry = new CommandRegistry();
        $expectedCreateCommand = $this->getMock('Entvalley\AppBundle\Tests\Domain\Command\CreateCommandStub');
        $expectedCreateCommand->expects($this->atLeastOnce())
            ->method('execute')
            ->will($this->returnValue(true));
        $expectedCreateCommand->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('create'));
        $registry->register($expectedCreateCommand);

        $userContextMock = $this->getMock('Entvalley\AppBundle\Domain\UserContextInterface');
        $statsService = $this->getMock('Entvalley\AppBundle\Service\StatsServiceInterface');

        $interpreter = new CommandInterpreter($registry, $userContextMock, $statsService);

        $source = "@create a new ticket\r\n";
        $source .= "@create another ticket";

        $statsService->expects($this->exactly(2))
            ->method('count');

        $interpreter->interpret(new CommandSource($source));
    }

    private function createInterpreterThatExpects($createExpected = false, $closeExpected = false)
    {
        $registry = new CommandRegistry();

        $expectedCreateCommand = $this->getMock('Entvalley\AppBundle\Tests\Domain\Command\CreateCommandStub');
        if ($createExpected !== false) {
            $expectedCreateCommand->expects($this->atLeastOnce())
                ->method('execute')
                ->with($createExpected)
                ->will($this->returnValue(true));
        } else {
            $expectedCreateCommand
                ->expects($this->never())
                ->method('execute');
        }
        $expectedCreateCommand->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('create'));
        $expectedCreateCommand->expects($this->any())
            ->method('isSatisfiedBySource')
            ->will($this->returnValue(true));

        $expectedCloseCommand = $this->getMock('Entvalley\AppBundle\Tests\Domain\Command\CloseCommandStub');
        if ($closeExpected !== false) {
            $expectedCloseCommand->expects($this->once())
                ->method('execute')
                ->with($closeExpected)
                ->will($this->returnValue(true));
        } else {
            $expectedCloseCommand
                ->expects($this->never())
                ->method('execute');
        }
        $expectedCloseCommand->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('close'));

        $registry->register($expectedCreateCommand);
        $registry->register($expectedCloseCommand);


        $userContextMock = $this->getMock('Entvalley\AppBundle\Domain\UserContextInterface');
        $statsService = $this->getMock('Entvalley\AppBundle\Service\StatsServiceInterface');

        return new CommandInterpreter($registry, $userContextMock, $statsService);
    }

}