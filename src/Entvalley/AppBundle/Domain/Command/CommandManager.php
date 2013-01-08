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
            $configs[Command::PREFIX . $command->getName()] = $this->extractConfig($command);
        }

        return $configs;
    }

    public function getCommandConfig($commandName)
    {
        return $this->extractConfig($this->registry->get($commandName));
    }

    /**
     * @param $command
     * @return array
     */
    private function extractConfig($command)
    {
        return [
            'is_visible' => $command->isVisible(),
            'applicable_in' => $command->getApplicableInText()
        ];
    }
}
