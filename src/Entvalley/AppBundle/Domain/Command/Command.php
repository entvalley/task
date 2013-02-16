<?php

namespace Entvalley\AppBundle\Domain\Command;

/**
 * Every command must implement Command interface
 *
 * @package Entvalley\AppBundle\Domain\Command
 */
interface Command
{
    /**
     * The prefix for all commands.
     *
     * To call a command, a new line must start with the prefix and command name
     */
    const PREFIX = "@";

    /**
     * Executes the command and returns its result. Command is believed to be executed if it returns
     * any non-empty value
     *
     * @param $content
     * @return mixed if a command succeed, it must return anything except for an empty value
     */
    function execute($content);

    /**
     * A unique name of the command
     *
     * @return string
     */
    function getName();

    /**
     * Sets the source that holds all information about the context of execution.
     *
     * @param CommandSource $source
     */
    function setSource(CommandSource $source);

    /**
     * Determines whether the command is visible or not.
     *
     * Returns true if the command is visible and available for public use.
     * Returns false it's an internal command.
     *
     * WARNING: If a command is not visible, a user still can call it
     *
     * @return mixed
     */
    function isVisible();


    /**
     * Determines whether the command can be executed in the given context
     *
     * If a given input doesn't include command name, the interpreter will try to guess
     * the command name based on the execution context by calling this method
     *
     * Please make sure there's only one satisfied command per context.
     *
     * @param CommandSource $source
     * @return bool
     */
    function isSatisfiedBySource(CommandSource $source);
}