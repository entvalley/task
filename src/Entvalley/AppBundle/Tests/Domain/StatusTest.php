<?php

namespace Entvalley\AppBundle\Tests\Domain;

use Entvalley\AppBundle\Domain\Status;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldSupportKnownStatuses()
    {
        $this->assertTrue(Status::isStatus('wontfix'));
        $this->assertTrue(Status::isStatus('CLOSED'));
        $this->assertFalse(Status::isStatus('_teststatus'));
    }
}