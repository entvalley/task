<?php

namespace Entvalley\AppBundle\Domain\Command;

interface Command
{
    const PREFIX = "@";

    function execute($content);
    function getName();
    function setSource(CommandSource $source);
    function isVisible();
}