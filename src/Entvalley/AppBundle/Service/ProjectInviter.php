<?php

namespace Entvalley\AppBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\Debug;
use Entvalley\AppBundle\Entity\Project;
use Entvalley\AppBundle\Entity\ProjectCollaborator;
use Entvalley\AppBundle\Entity\ProjectInvitation;
use FOS\UserBundle\Model\UserInterface;

class ProjectInviter
{
    private $em;
    private $templatedMailer;
    private $router;

    public function __construct(ObjectManager $em, TemplatedMailer $templatedMailer, $router)
    {
        $this->em = $em;
        $this->templatedMailer = $templatedMailer;
        $this->router = $router;
    }

    public function invite(Project $project, array $invitations, UserInterface $byUser)
    {
        $projectInvitationRepository = $this->em->getRepository('Entvalley\AppBundle\Entity\ProjectInvitation');

        $invitedEmails = array();

        /** @var $invitation ProjectInvitation */
        foreach ($invitations as $invitation) {
            $invitation->setProject($project);
            $invitation->setInvitedBy($byUser);
            $invitedEmails[] = $invitation->getInviteeEmail();
        }

        // if already invited - resend a new invitation and update the sender
        $alreadyInvitedPeople = $projectInvitationRepository->findByEmails($project, $invitedEmails);
        foreach ($alreadyInvitedPeople as $alreadyInvitedPerson) {
            foreach ($invitations as $key => $invitation) {
                if ($invitation->equals($alreadyInvitedPerson)) {
                    $alreadyInvitedPerson->setInvitedBy($byUser);
                    $this->sendMail($alreadyInvitedPerson);
                    $alreadyInvitedPerson->updateInvitedAtDate();
                    unset($invitations[$key]);
                }
            }
        }

        // If a user hasn't been invited, create a new invitation and send an email
        foreach ($invitations as $invitation) {
            $this->sendMail($invitation);
            $this->em->persist($invitation);
        }
    }

    public function accept(ProjectInvitation $invitation, $user)
    {
        if ($invitation->isAccepted()) {
            return false;
        }

        // create a collaborator;
        // set invitation as accepted.
        $collaborator = new ProjectCollaborator();
        $collaborator->setProject($invitation->getProject());
        $collaborator->setCollaborator($user);
        $this->em->persist($collaborator);

        $invitation->accept();

        return $collaborator;
    }

    protected function sendMail(ProjectInvitation $invitation)
    {
        $invitationLink = $this->router->generate('app_project_accept_invitation', [
                'company' => $invitation->getInvitedBy()->getCompanyId(),
                'hash' => $invitation->getPublicHash()
            ]);

        $this->templatedMailer->send(
            'Invitation to ' . $invitation->getProjectName(),
            $invitation->getInviteeEmail(),
            'EntvalleyAppBundle:mail_templates:project_invitation.html.twig',
            [
                'invited_by' => $invitation->getInvitedBy(),
                'invited_to' => $invitation->getProjectName(),
                'invitation_link' => $invitationLink
            ]
        );
    }
}
