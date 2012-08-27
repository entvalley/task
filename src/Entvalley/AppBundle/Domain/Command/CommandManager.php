<?php

namespace Entvalley\AppBundle\Domain\Command;

class CommandManager
{
    private $registry;
    private $interpreter;

    public function __construct(CommandInterpreter $interpreter, $registry)
    {
        $this->interpreter = $interpreter;
        $this->registry = $registry;
    }

    public function extractCommands($source)
    {
        return $this->interpreter->interpret($source);
    }

    public function getCommandNames()
    {
        return array_map(function ($val) {
            return Command::PREFIX . $val;
        }, $this->registry->getRegisteredNames());
    }
}
