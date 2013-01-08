<?php

namespace Entvalley\AppBundle\Tests\Service;

use Entvalley\AppBundle\Service\ProjectInviter;
use Entvalley\AppBundle\Tests\Service\Mock\ProjectInvitationRepositoryMock;

class ProjectInviterTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldInviteNewPerson()
    {
        $user = $this->getUser();
        $project = $this->getMock('Entvalley\AppBundle\Entity\Project');
        $invitation = $this->getMock('Entvalley\AppBundle\Entity\ProjectInvitation');
        $invitationRepository = new ProjectInvitationRepositoryMock(array());

        $em = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($invitationRepository));
        $em->expects($this->once())
            ->method('persist');

        $invitation->expects($this->once())
            ->method('setProject')
            ->with($project);
        $invitation->expects($this->any())
            ->method('getInvitedBy')
            ->will($this->returnValue($user));

        $inviter = $this->createProjectInviter($em);
        $inviter->invite($project, array($invitation), $user);
    }

    public function testShouldUpdateAlredyInvitedPerson()
    {
        $user = $this->getUser();
        $project = $this->getMock('Entvalley\AppBundle\Entity\Project');
        $invitation = $this->getMock('Entvalley\AppBundle\Entity\ProjectInvitation');
        $invitationRepository = new ProjectInvitationRepositoryMock(array($invitation));

        $em = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($invitationRepository));
        $em->expects($this->never())
            ->method('persist');

        $invitation->expects($this->once())
            ->method('equals')
            ->will($this->returnValue(true));
        $invitation->expects($this->once())
            ->method('updateInvitedAtDate');
        $invitation->expects($this->any())
            ->method('getInvitedBy')
            ->will($this->returnValue($user));

        $inviter = $this->createProjectInviter($em);
        $inviter->invite($project, array($invitation), $user);
    }

    public function testShouldCreateCollaboratorAndMarkInvitationAsAccepted()
    {
        $user = $this->getUser();
        $project = $this->getMock('Entvalley\AppBundle\Entity\Project');
        $invitation = $this->getMock('Entvalley\AppBundle\Entity\ProjectInvitation');

        $em = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $em->expects($this->once())
            ->method('persist');
        $invitation->expects($this->once())
            ->method('getProject')
            ->will($this->returnValue($project));
        $invitation->expects($this->once())
            ->method('accept');

        $inviter = $this->createProjectInviter($em, true);
        $collaborator = $inviter->accept($invitation, $user);
        $this->assertEquals($user, $collaborator->getCollaborator());
        $this->assertEquals($project, $collaborator->getProject());
    }

    public function testShouldNotCreateCollaboratorIfInvitationWasAcceptedBefore()
    {
        $user = $this->getUser();
        $project = $this->getMock('Entvalley\AppBundle\Entity\Project');
        $invitation = $this->getMock('Entvalley\AppBundle\Entity\ProjectInvitation');

        $em = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $em->expects($this->never())
            ->method('persist');
        // need a project to prevent fatal errors instead of failed test cases
        $invitation->expects($this->any())
            ->method('getProject')
            ->will($this->returnValue($project));
        $invitation->expects($this->once())
            ->method('isAccepted')
            ->will($this->returnValue(true));

        $inviter = $this->createProjectInviter($em, true);
        $this->assertFalse($inviter->accept($invitation, $user));
    }

    private function getUser()
    {
        return $this->getMock('Entvalley\AppBundle\Entity\User');
    }

    private function createProjectInviter($em, $doNotSendMail = false)
    {
        $mailer = $this->getMock('Entvalley\AppBundle\Service\TemplatedMailer', array(), array(), '', false);
        if ($doNotSendMail == false) {
            $mailer->expects($this->once())
                ->method('send');
        }

        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $router->expects($this->any())
            ->method('generate')
            ->will($this->returnValue('#invitation_link'));

        return new ProjectInviter($em, $mailer, $router);
    }
}