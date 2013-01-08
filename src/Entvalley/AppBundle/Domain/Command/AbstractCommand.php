<?php

namespace Entvalley\AppBundle\Domain\Command;

abstract class AbstractCommand implements Command
{
    protected $content;

    /**
     * @var $source CommandSource
     */
    protected $source;

    protected $isVisible = true;

    public function setSource(CommandSource $source)
    {
        $this->source = $source;
    }

    public function isGuessableBySource(CommandSource $source)
    {
        return false;
    }

    public function isVisible()
    {
        return $this->isVisible;
    }

    /**
     * Return a hint that will help a user to understand where he/she
     * can apply this command in.
     * Must be redefined in every command.
     *
     * @return string
     */
    public function getApplicableInText()
    {
        return 'a task or project';
    }
}