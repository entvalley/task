<?php

namespace Entvalley\AppBundle\Entity;

class StatusChange
{
    private $id;
    private $whoUpdated;
    private $status;
    private $task;
    private $createdAt;

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

}
