<?php

namespace Entvalley\AppBundle\Tests\Entity;

use Entvalley\AppBundle\Entity\Project;
use Entvalley\AppBundle\Entity\Company;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldUseCanonicalGeneratorForCanonicalName()
    {
        $project = new Project();
        $project->setName(" weird  name_ ! 123 #/");

        $this->assertEquals("weird-name_-123", $project->getCanonicalName());
    }

    public function testShouldBelongToCompanyThatProjectWasCreatedIn()
    {
        $company = new Company(1234);
        $anotherCompany = new Company(4321);
        $project = new Project();
        $project->setCompany($company);

        $this->assertTrue($project->belongsToCompany($company));
        $this->assertFalse($project->belongsToCompany($anotherCompany));
    }
}
