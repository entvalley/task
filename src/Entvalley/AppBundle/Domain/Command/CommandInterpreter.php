<?php

namespace Entvalley\AppBundle\Domain\Command;

use Entvalley\AppBundle\Domain\UserContextInterface;
use Entvalley\AppBundle\Service\StatsServiceInterface;

class CommandInterpreter
{
    private $registry;
    private $userContext;
    private $statsService;

    public function __construct(CommandRegistry $registry, UserContextInterface $userContext, StatsServiceInterface $statsService)
    {
        $this->registry = $registry;
        $this->statsService = $statsService;
        $this->userContext = $userContext;
    }

    public function interpret(CommandSource $source)
    {
        $result = [];

        $matches = $this->splitCommands($source->getText());

        reset($matches);

        if ($this->commandNeedsToBeGuessed($matches)) {
            $result = $this->guessCommand($source, current($matches));
        }

        while (next($matches)) {
            $commandName = ltrim(current($matches), Command::PREFIX . ' ');
            $command = $this->registry->get($commandName);
            $command->setSource($source);
            $result[$commandName][] = $this->execute($command, next($matches));
        }

        return empty($result) ? false : $result;
    }

    private function commandNeedsToBeGuessed($matches)
    {
        return trim(current($matches)) !== '';
    }

    private function guessCommand(CommandSource $source, $content)
    {
        $result = [];
        foreach ($this->registry->getAll() as $name => $command) {
            if ($command->isSatisfiedBySource($source)) {
                $command->setSource($source);
                $result[$name][] = $this->execute($command, $content);
            }
        }
        return $result;
    }

    private function splitCommands($text)
    {
        $expr =  Command::PREFIX . implode('|' . Command::PREFIX, $this->registry->getRegisteredNames());
        return preg_split('~(?:^(' . $expr . ')(?: |$))~m', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    }

    private function execute(AbstractCommand $command, $content)
    {
        $result = $command->execute(trim($content));
        $normalizedUserName = $this->userContext->getUser() ? preg_replace('~[^\w\d]~', '_', (string)$this->userContext->getUser()) : '$unknown$';
        $this->statsService->count("task.command.{$normalizedUserName}.{$command->getName()}");
        return $result;
    }
}
