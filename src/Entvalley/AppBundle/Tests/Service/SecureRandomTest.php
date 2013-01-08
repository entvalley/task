<?php

namespace Entvalley\AppBundle\Tests\Service;

use Entvalley\AppBundle\Service\SecureRandom;

class SecureRandomTest extends \PHPUnit_Framework_TestCase
{

    public function testShouldGenerateTwoDifferentNumbers()
    {
        $random1 = SecureRandom::rand(20);
        $random2 = SecureRandom::rand(20);

        $this->assertNotEquals($random1, $random2, "must be two different random numbers");
        $this->assertEquals(20, strlen($random1));
        $this->assertEquals(20, strlen($random2));
    }

    public function testShouldGenerateTwoDifferentNumbersInNonSecureMode()
    {
        $random1 = SecureRandom::rand(20, false);
        $random2 = SecureRandom::rand(20, false);

        $this->assertNotEquals($random1, $random2, "must be two different random numbers");
        $this->assertEquals(20, strlen($random1));
        $this->assertEquals(20, strlen($random2));
    }

}
