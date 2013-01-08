<?php

namespace Entvalley\AppBundle\Tests\Security;

use Entvalley\AppBundle\Security\InvitedToEvaluator;

class InvitedToEvaluatorTest extends \PHPUnit_Framework_TestCase
{
    public function testObjectsThatHaveNoOwnerShouldBelongToEveryone()
    {
        $this->markTestSkipped();
        $user = $this->getMock('Entvalley\AppBundle\Entity\User');
        $evaluator = new InvitedToEvaluator();

        $this->assertTrue($evaluator->belongsTo(new \stdClass(), $user));
    }
}