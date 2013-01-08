<?php

namespace Entvalley\AppBundle\Tests\Security;

use Entvalley\AppBundle\Security\OwnerEvaluator;

class OwnerEvaluatorTest extends \PHPUnit_Framework_TestCase
{
    public function testObjectsThatHaveNoOwnerShouldBelongToEveryone()
    {
        $user = $this->getMock('Entvalley\AppBundle\Entity\User');
        $evaluator = new OwnerEvaluator();

        $this->assertTrue($evaluator->belongsTo(new \stdClass(), $user));
    }

    public function testShouldEvaluateToTrueIfObjectBelongsToUser()
    {
        $user = $this->getMock('Entvalley\AppBundle\Entity\User');
        $evaluator = new OwnerEvaluator();

        $object = $this->getMock('Entvalley\AppBundle\Domain\IHaveOwner');
        $object->expects($this->once())
            ->method('isBelongingTo')
            ->will($this->returnValue(true));

        $this->assertTrue($evaluator->belongsTo($object, $user));
    }

    public function testShouldEvaluateToFalseIfObjectDoesNotBelongToUser()
    {
        $user = $this->getMock('Entvalley\AppBundle\Entity\User');
        $evaluator = new OwnerEvaluator();

        $object = $this->getMock('Entvalley\AppBundle\Domain\IHaveOwner');
        $object->expects($this->once())
            ->method('isBelongingTo')
            ->will($this->returnValue(false));

        $this->assertFalse($evaluator->belongsTo($object, $user));
    }
}