<?php

namespace Entvalley\AppBundle\Tests\Entity;

use Entvalley\AppBundle\Entity\Task;
use Entvalley\AppBundle\Entity\Comment;
use Entvalley\AppBundle\Domain\Status;

class TaskTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldUseCanonicalGeneratorForCanonicalTitle()
    {
        $task = new Task;
        $task->setTitle(" weird  titile_ ! 123 #/");

        $this->assertEquals("weird-titile_-123", $task->getCanonicalTitle());
    }

    public function testShouldSplitContentIntoTitleAndText()
    {
        $task = new Task;
        $task->setBodyWithTitle("It's a title\r\nand it's not a title\r\njusttext");

        $this->assertEquals("It's a title", $task->getTitle());
        $this->assertEquals("and it's not a title\r\njusttext", $task->getBody());
    }

    public function testShouldSplitContentIntoTitle()
    {
        $task = new Task;
        $task->setBodyWithTitle("It's a title");

        $this->assertEquals("It's a title", $task->getTitle());
        $this->assertEmpty($task->getBody());
    }

    public function testShouldCreateStatusChangeOnStatusChange()
    {
        $userMock = $this->getMock('\Entvalley\AppBundle\Entity\User');

        $task = new Task;

        $this->assertEmpty($task->getLastStatus());

        $task->setStatus($userMock, Status::CLOSED);
        $result = $task->getLastStatus();

        $this->assertInstanceOf('\Entvalley\AppBundle\Entity\StatusChange', $result);
        $this->assertEquals(Status::CLOSED, $result->getStatus());
        $this->assertEquals($userMock, $result->getWhoUpdated());
    }

    public function testShouldChangeNumberOfCommentsWhenAddingOrRemovingComments()
    {
        $task = new Task();

        $this->assertEquals(0, count($task->getComments()));
        $this->assertEquals(0, $task->getNumberOfComments());

        $task->addComment($comment = new Comment());

        $this->assertEquals(1, count($task->getComments()));
        $this->assertEquals(1, $task->getNumberOfComments());

        $task->removeComment($comment);

        $this->assertEquals(0, count($task->getComments()));
        $this->assertEquals(0, $task->getNumberOfComments());
    }
}