<?php

namespace Entvalley\AppBundle\Domain\Command;

abstract class AbstractCommand implements Command
{
    protected $content;

    /**
     * @var $source CommandSource
     */
    protected $source;

    public function setSource(CommandSource $source)
    {
        $this->source = $source;
    }

    public function isGuessableBySource(CommandSource $source)
    {
        return false;
    }
}