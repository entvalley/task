<?php

namespace Entvalley\AppBundle\Tests\Domain;

use Entvalley\AppBundle\Domain\Status;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldSupportKnownStatuses()
    {
        $this->assertTrue(Status::supports('wontfix'));
        $this->assertTrue(Status::supports('CLOSED'));
        $this->assertFalse(Status::supports('_teststatus'));
    }
}