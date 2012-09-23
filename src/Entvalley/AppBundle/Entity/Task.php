<?php

namespace Entvalley\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Entvalley\AppBundle\Domain\Status;

class Task
{
    private $id;
    private $title;
    private $text;
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

    public function setAssignedTo($assignedTo)
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

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
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

    public function setTextWithTitle($text)
    {
        $textParts = preg_split("~(\n|\r)~", $text, 2);

        $this->title = trim($textParts[0]);
        $this->text = isset($textParts[1]) ? trim($textParts[1]) : "";
    }

    public function setStatus(User $whoUpdated, $status)
    {
        $statusHistory = new StatusHistory();
        $statusHistory->setTask($this);
        $statusHistory->setStatus($status);
        $statusHistory->setWhoUpdated($whoUpdated);
        $this->status = $status;
        $this->lastStatus = $statusHistory;
    }

    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return StatusHistory
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