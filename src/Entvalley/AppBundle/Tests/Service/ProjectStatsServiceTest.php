<?php

namespace Entvalley\AppBundle\Tests\Service;

use Entvalley\AppBundle\Service\ProjectStatsService;

class ProjectStatsServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldCallCallbackToRetrieveStats()
    {
        $called = false;

        $callback = function () use (&$called) {
            $called = true;
        };

        $stats = new ProjectStatsService($callback);

        $this->assertFalse($called);

        $stats->getInprogressNumber();
        $this->assertTrue($called);
    }

    public function testShouldNotCallCallbackWhenStatsAreLoaded()
    {
        $called = false;

        $callback = function () use (&$called) {
            $called = true;
        };

        $stats = new ProjectStatsService($callback);
        $stats->setInprogressNumber(123);
        $stats->getInprogressNumber();
        $this->assertFalse($called);
    }

}