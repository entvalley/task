<?php

namespace Entvalley\AppBundle\Tests\Domain;

use Entvalley\AppBundle\Domain\ProjectCollaboratorService;
use Entvalley\AppBundle\Entity\Project;
use Entvalley\AppBundle\Entity\ProjectCollaborator;
use Entvalley\AppBundle\Entity\User;

class ProjectCollaboratorServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldBeCollaboratorIfUserIsInCollaborators()
    {
        $projectCollaboratorRepository = $this->getMock('Entvalley\AppBundle\Domain\IProjectCollaboratorRepository');
        $projectCollaboratorRepository->expects($this->once())
            ->method('findByUserAndProject')
            ->will($this->returnValue(new ProjectCollaborator()));

        $projectCollaboratorService = new ProjectCollaboratorService($projectCollaboratorRepository);

        $this->assertTrue($projectCollaboratorService->isCollaborator(new User(), new Project()));
    }

    public function testShouldBeNotCollaboratorIfUserIsNotInCollaborators()
    {
        $projectCollaboratorRepository = $this->getMock('Entvalley\AppBundle\Domain\IProjectCollaboratorRepository');
        $projectCollaboratorRepository->expects($this->once())
            ->method('findByUserAndProject')
            ->will($this->returnValue(null));

        $projectCollaboratorService = new ProjectCollaboratorService($projectCollaboratorRepository);

        $this->assertFalse($projectCollaboratorService->isCollaborator(new User(), new Project()));
    }
}