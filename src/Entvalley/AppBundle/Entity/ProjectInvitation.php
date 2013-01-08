<?php

namespace Entvalley\AppBundle\Entity;

use Entvalley\AppBundle\Service\SecureRandom;

class ProjectInvitation
{
    private $id;

    /**
     * @var Project $project
     */
    private $project;

    /**
     * @var User $invitedBy
     */
    private $invitedBy;
    private $invitedAt;
    private $isAccepted = false;
    private $inviteeEmail;
    private $publicHash;

    public function __construct()
    {
        $this->updateInvitedAtDate();
        $this->publicHash = bin2hex(SecureRandom::rand(16));
    }

    public function updateInvitedAtDate()
    {
        $this->invitedAt = new \DateTime();
    }

    public function getInvitedAt()
    {
        return $this->invitedAt;
    }

    public function setInviteeEmail($inviteeEmail)
    {
        $this->inviteeEmail = strtolower($inviteeEmail);
    }

    public function getInviteeEmail()
    {
        return $this->inviteeEmail;
    }

    public function accept()
    {
        $this->isAccepted = true;
    }

    public function isAccepted()
    {
        return $this->isAccepted;
    }

    /**
     * @param \Entvalley\AppBundle\Entity\User $invitedBy
     */
    public function setInvitedBy(User $invitedBy)
    {
        $this->invitedBy = $invitedBy;
    }

    /**
     * @return \Entvalley\AppBundle\Entity\User
     */
    public function getInvitedBy()
    {
        return $this->invitedBy;
    }

    /**
     * @param \Entvalley\AppBundle\Entity\Project $project
     */
    public function setProject(Project $project)
    {
        $this->project = $project;
    }

    /**
     * @return \Entvalley\AppBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getProjectName()
    {
        $project = $this->getProject();
        return $project ? $project->getName() : '';
    }

    public function getInvitedByName()
    {
        $invitedBy = $this->getInvitedBy();
        return $invitedBy ? $invitedBy->getEmail() : '';
    }

    public function getPublicHash()
    {
        return $this->publicHash;
    }

    public function equals(ProjectInvitation $another)
    {
        return $this->getInviteeEmail() === $another->getInviteeEmail()
            && $this->getProject()->getId() === $another->getProject()->getId();
    }
}
