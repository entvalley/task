<?php

namespace Entvalley\AppBundle\Tests\Entity\Factory;

use Entvalley\AppBundle\Entity\Factory\TaskFactory;
use Entvalley\AppBundle\Entity\User;
use Entvalley\AppBundle\Domain\Status;

class TaskFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldCreateEmptyTaskWithDateAndNewState()
    {
        $user = new User;
        $taskFactory = new TaskFactory;
        $task = $taskFactory->createFor($user);

        $this->assertEquals(Status::UNASSIGNED, $task->getStatus());
        $this->assertNotEmpty($task->getCreatedAt());
        $this->assertEquals($user, $task->getAuthor());
    }
}