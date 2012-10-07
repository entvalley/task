<?php

namespace Entvalley\AppBundle\Domain\Command;

class CommandSource
{
    private $text;
    private $contextId = 0;
    private $contextType;

    public function __construct($text = null)
    {
        $this->text = $text;
    }

    public function setContextType($contextType)
    {
        $this->contextType = $contextType;
    }

    public function getContextType()
    {
        return $this->contextType;
    }

    public function setContextId($contextId)
    {
        $this->contextId = (int)$contextId;
    }

    public function getContextId()
    {
        return $this->contextId;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
    }
}
