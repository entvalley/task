<?php

namespace Entvalley\AppBundle\Tests\Entity\Factory;

use Entvalley\AppBundle\Entity\Task;
use Entvalley\AppBundle\Domain\Status;

class TaskTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldSplitContentIntoTitleAndText()
    {
        $task = new Task;
        $task->setTextWithTitle("It's a title\r\nand it's not a title\r\njusttext");

        $this->assertEquals("It's a title", $task->getTitle());
        $this->assertEquals("and it's not a title\r\njusttext", $task->getText());
    }

    public function testShouldSplitContentIntoTitle()
    {
        $task = new Task;
        $task->setTextWithTitle("It's a title");

        $this->assertEquals("It's a title", $task->getTitle());
        $this->assertEmpty($task->getText());
    }

    public function testShouldCreateStatusHistoryOnStatusChange()
    {
        $userMock = $this->getMock('\Entvalley\AppBundle\Entity\User');

        $task = new Task;
        $task->setStatus($userMock, Status::CLOSED);

        $result = $task->getLastStatus();

        $this->assertInstanceOf('\Entvalley\AppBundle\Entity\StatusHistory', $result);
        $this->assertEquals(Status::CLOSED, $result->getStatus());
        $this->assertEquals($userMock, $result->getWhoUpdated());
    }

}