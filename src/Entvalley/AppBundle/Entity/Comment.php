<?php

namespace Entvalley\AppBundle\Entity;

class Comment
{
    private $id;
    private $author;
    private $text;
    private $createdAt;
    /**
     * @var Task
     */
    private $task;

    /**
     * @var StatusChange $statusChange
     */
    private $statusChange;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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

    public function setTask(Task $task)
    {
        $this->task = $task;
        $task->addComment($this);
    }

    public function getTask()
    {
        return $this->task;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
    }

    /**
     * @param \Entvalley\AppBundle\Entity\StatusChange $statusChange
     */
    public function setStatusChange(StatusChange $statusChange = null)
    {
        $this->statusChange = $statusChange;
    }

    /**
     * @return \Entvalley\AppBundle\Entity\StatusChange
     */
    public function getStatusChange()
    {
        return $this->statusChange;
    }

    public function equals(Comment $comment)
    {
        return $comment === $this || $comment->getId() === $this->getId();
    }

    public function removeCommentFromTask()
    {
        $this->task->removeComment($this);
    }
}
