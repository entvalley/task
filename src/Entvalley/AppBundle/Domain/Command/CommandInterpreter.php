<?php

namespace Entvalley\AppBundle\Domain\Command;

use RuntimeException;

class CommandInterpreter
{
    private $registry;

    public function __construct(CommandRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function interpret(CommandSource $source)
    {
        $result = array();

        $matches = $this->splitCommands($source->getText());

        reset($matches);

        if ($this->commandNeedsToBeGuessed($matches)) {
            $result = array_merge($result, $this->guessCommand($source, current($matches)));
        }

        while (next($matches)) {
            $commandName = ltrim(current($matches), Command::PREFIX . ' ');
            $command = $this->registry->get($commandName);
            $command->setSource($source);
            $result[$commandName][] = $command->execute(trim(next($matches)));
        }

        return empty($result) ? false : $result;
    }

    private function commandNeedsToBeGuessed($matches)
    {
        return trim(current($matches)) !== '';
    }

    private function guessCommand(CommandSource $source, $content)
    {
        $result = array();
        foreach ($this->registry->getAll() as $name => $command) {
            if ($command->isGuessableBySource($source)) {
                $command->setSource($source);
                $result[$name][] = $command->execute(trim($content));
            }
        }
        return $result;
    }

    private function splitCommands($text)
    {
        $expr =  Command::PREFIX . implode('|' . Command::PREFIX, $this->registry->getRegisteredNames());
        return preg_split('~(?:^(' . $expr . ')(?: |$))~m', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    }
}
