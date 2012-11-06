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

    public function getCommandsConfigs()
    {
        $commands = $this->registry->getAll();

        $configs = [];

        foreach ($commands as $command) {
            $configs[Command::PREFIX . $command->getName()] = [
                'is_visible' => $command->isVisible()
            ];
        }

        return $configs;
    }
}
