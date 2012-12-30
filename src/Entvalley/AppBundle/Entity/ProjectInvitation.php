<?php

namespace Entvalley\AppBundle\Entity;

use Entvalley\AppBundle\Domain\IHaveOwner;

class ProjectInvitation implements IHaveOwner
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
    private $createdAt;
    private $isAccepted = false;
    private $inviteeEmail;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setInviteeEmail($inviteeEmail)
    {
        $this->inviteeEmail = $inviteeEmail;
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

    public function isBelongingTo(User $user)
    {
        return $this->getProject()->isBelongingTo($user);
    }
}
