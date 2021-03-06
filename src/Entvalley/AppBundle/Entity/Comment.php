<?php

namespace Entvalley\AppBundle\Entity;

use Entvalley\AppBundle\Domain\IHaveProject;
use HTMLPurifier;
use Entvalley\AppBundle\Domain\IHaveOwner;

class Comment implements IHaveOwner, IHaveProject
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
     * Status change that the comment belongs to (if any)
     *
     * @var StatusChange $statusChange
     */
    private $statusChange;

    /**
     * @var $htmlPurifier HTMLPurifier
     */
    private $htmlPurifier;
    private $safeText;

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
        $this->safeText = null;
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

    public function getSafeText()
    {
        if (empty($this->safeText)) {
            $this->purifyHtmlTags();
        }
        return $this->safeText;
    }

    /**
     * Serializer callback to generate a text with safe HTML tags which can be displayed
     * in the browser. Requires a link to html purifier service.
     *
     * @see Task::setHtmlPurifier
     */
    public function purifyHtmlTags()
    {
        if ($this->htmlPurifier) {
            $this->safeText = $this->htmlPurifier->purify($this->text);
        }
    }

    public function setHtmlPurifier($htmlPurifier)
    {
        $this->htmlPurifier = $htmlPurifier;
    }

    public function isBelongingTo(User $user)
    {
        if (empty($this->task)) {
            return true;
        }
        return $this->getTask()->isBelongingTo($user);
    }

    public function getProject()
    {
        return $this->task->getProject();
    }

    public function hasStatusChange()
    {
        return !empty($this->statusChange);
    }
}
