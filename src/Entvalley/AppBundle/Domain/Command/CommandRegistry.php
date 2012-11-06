<?php

namespace Entvalley\AppBundle\Domain\Command;

class CommandRegistry
{
    private $registry = [];

    public function register(Command $command)
    {
        $this->registry[$command->getName()] = $command;
    }

    public function get($name)
    {
        if (isset($this->registry[$name])) {
            return $this->registry[$name];
        }
        return null;
    }

    public function getAll()
    {
        return $this->registry;
    }

    public function getRegisteredNames()
    {
        return array_keys($this->registry);
    }
}
