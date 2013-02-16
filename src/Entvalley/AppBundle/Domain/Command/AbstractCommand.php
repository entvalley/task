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

    /**
     * {@inheritDoc}
     */
    public function setSource(CommandSource $source)
    {
        $this->source = $source;
    }

    /**
     * If a given input doesn't include command name, the interpreter will try to guess
     * the command name based on the execution context
     *
     * @param CommandSource $source
     * @return bool
     */
    public function isSatisfiedBySource(CommandSource $source)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isVisible()
    {
        return $this->isVisible;
    }

    /**
     * {@inheritDoc}
     */
    public function getApplicableInText()
    {
        return 'a task or project';
    }
}