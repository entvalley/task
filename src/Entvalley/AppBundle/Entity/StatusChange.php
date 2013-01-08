<?php

namespace Entvalley\AppBundle\Entity;

use Entvalley\AppBundle\Domain\IHaveOwner;
use Entvalley\AppBundle\Domain\IHaveProject;

class StatusChange implements IHaveOwner, IHaveProject
{
    private $id;
    private $whoUpdated;
    private $status;

    /**
     * @var Task
     */
    private $task;
    private $createdAt;
    private $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setTask(Task $task)
    {
        $this->task = $task;
    }

    public function getTask()
    {
        return $this->task;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setWhoUpdated(User $whoUpdated)
    {
        $this->whoUpdated = $whoUpdated;
    }

    public function getWhoUpdated()
    {
        return $this->whoUpdated;
    }

    public function isBelongingTo(User $user)
    {
        return $this->task->isBelongingTo($user);
    }

    public function getProject()
    {
        return $this->task->getProject();
    }
}
