<?php

namespace Entvalley\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Entvalley\AppBundle\Domain\Status;

class Task
{
    private $id;
    private $title;
    private $body;
    private $author;
    private $assignedTo;
    private $createdAt;
    private $lastModification;
    private $comments;
    private $company;
    private $lastStatus;
    private $status;
    private $numberComments = 0;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->comments = new ArrayCollection();
        $this->status = Status::UNASSIGNED;
    }

    public function setAssignedTo($assignedTo = null)
    {
        $this->assignedTo = $assignedTo;
    }

    public function setCompany($company)
    {
        $this->company = $company;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function getAssignedTo()
    {
        return $this->assignedTo;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setLastModification($lastModification)
    {
        $this->lastModification = $lastModification;
    }

    public function getLastModification()
    {
        return $this->lastModification;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;
        $this->numberComments++;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function setBodyWithTitle($text)
    {
        $textParts = preg_split("~(\n|\r)~", $text, 2);

        $this->title = trim($textParts[0]);
        $this->body = isset($textParts[1]) ? trim($textParts[1]) : "";
    }

    public function setStatus(User $whoUpdated, $status)
    {
        $statusChange = new StatusChange();
        $statusChange->setTask($this);
        $statusChange->setStatus($status);
        $statusChange->setWhoUpdated($whoUpdated);
        $this->status = $status;
        $this->lastStatus = $statusChange;
    }

    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return StatusChange
     */
    public function getLastStatus()
    {
        return $this->lastStatus;
    }

    public function getNumberComments()
    {
        return $this->numberComments;
    }
}