<?php

namespace Entvalley\AppBundle\Tests\Domain;

use Entvalley\AppBundle\Domain\Status;
use Entvalley\AppBundle\Domain\TaskFilter;

class TaskFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldReturnReopenedAndAcceptedStatusesForInprogressFilter()
    {
        $taskFilter = new TaskFilter();
        $taskFilter->thatAre('inprogress');
        $this->assertEquals(array(Status::REOPENED, Status::ACCEPTED), $taskFilter->getStatuses());
    }

    public function testShouldReturnUnassignedStatusForUnresolvedFilter()
    {
        $taskFilter = new TaskFilter();
        $taskFilter->thatAre('unresolved');
        $this->assertEquals(array(Status::UNASSIGNED), $taskFilter->getStatuses());
    }

    public function testShouldReturnNullForUnknownFilter()
    {
        $taskFilter = new TaskFilter();
        $taskFilter->thatAre('_uknown_filter');
        $this->assertNull($taskFilter->getStatuses());
    }
}
