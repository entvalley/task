<?php

namespace Entvalley\AppBundle\Tests\Entity;

use Entvalley\AppBundle\Domain\CanonicalNameGenerator;
class CanonicalNameGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldGenerateCanonicalNameThatIsUsedAsIdWhenNameIsSet()
    {
        $generator = new CanonicalNameGenerator();
        $this->assertEquals("weird-name_-123", $generator->generate(" weird  name_ ! 123 #/"));
    }

    public function testShouldStripCanonicalNameLongerThan20Characters()
    {
        $generator = new CanonicalNameGenerator();
        $this->assertEquals("a-long-name-with-spe", $generator->generate("a ~@#$%%/ long name with special characters"));
    }

    public function testShouldRemoveAllSpaceCharacters()
    {
        $generator = new CanonicalNameGenerator();
        $this->assertEquals('name', $generator->generate("name \t" . json_decode('"\u200b"')));
    }
}
