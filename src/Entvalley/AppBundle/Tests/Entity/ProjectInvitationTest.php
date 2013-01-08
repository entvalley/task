<?php

namespace Entvalley\AppBundle\Tests\Entity;

use Entvalley\AppBundle\Entity\ProjectInvitation;

class ProjectInvitationTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldBeEqualIfProjectAndEmailAreSame()
    {
        $inviteeEmail = 'test@example.com';

        $project = $this->getMock('Entvalley\AppBundle\Entity\Project');
        $project->expects($this->any())->method('getId')->will($this->returnValue(1));

        $anotherProject = $this->getMock('Entvalley\AppBundle\Entity\Project');
        $anotherProject->expects($this->any())->method('getId')->will($this->returnValue(2));

        $invitation = new ProjectInvitation();
        $invitation->setInviteeEmail($inviteeEmail);
        $invitation->setProject($project);

        $secondInvitation = new ProjectInvitation();
        $secondInvitation->setProject($project);
        $secondInvitation->setInviteeEmail($inviteeEmail);

        $thirdInvitation = new ProjectInvitation();
        $thirdInvitation->setProject($anotherProject);
        $thirdInvitation->setInviteeEmail($inviteeEmail);


        $this->assertTrue($invitation->equals($secondInvitation));
        $this->assertFalse($invitation->equals($thirdInvitation));
    }

    public function testEmailShouldBeConvertedToLowerCase()
    {
        $invitation = new ProjectInvitation();
        $invitation->setInviteeEmail('TEST@example.com');
        $this->assertEquals('test@example.com', $invitation->getInviteeEmail());
    }

    public function testShouldHavePublicHashUponCreation()
    {
        $invitation = new ProjectInvitation();
        $this->assertNotEmpty($invitation->getPublicHash());
    }
}