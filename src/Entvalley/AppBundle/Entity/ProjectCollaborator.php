<?php

namespace Entvalley\AppBundle\Entity;

use Entvalley\AppBundle\Domain\IHaveOwner;

class ProjectCollaborator implements IHaveOwner
{
    private $id;

    /**
     * @var Project $project
     */
    private $project;

    /**
     * @var User $collaborator
     */
    private $collaborator;

    private $createdAt;

    /**
     * @param \Entvalley\AppBundle\Entity\User $collaborator
     */
    public function setCollaborator(User $collaborator)
    {
        $this->collaborator = $collaborator;
    }

    /**
     * @return \Entvalley\AppBundle\Entity\User
     */
    public function getCollaborator()
    {
        return $this->collaborator;
    }

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
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
