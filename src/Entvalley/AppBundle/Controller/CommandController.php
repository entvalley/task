<?php

namespace Entvalley\AppBundle\Controller;

use Entvalley\AppBundle\Domain\JsonEncoder;
use Entvalley\AppBundle\Domain\Command\CommandInterpreter;
use Symfony\Component\HttpFoundation\Request;
use Entvalley\AppBundle\Domain\Command\CommandManager;
use JMS\Serializer\SerializerInterface;
use Entvalley\AppBundle\Form\CommandType;
use Entvalley\AppBundle\Domain\Command\CommandSource;
use Symfony\Component\HttpFoundation\JsonResponse;

class CommandController extends Controller
{
    private $serializer;
    private $commandManager;
    private $commandInterpreter;

    public function __construct(
        ControllerContainer $container,
        SerializerInterface $serializer,
        CommandManager $commandManager,
        CommandInterpreter $commandInterpreter)
    {
        $this->serializer = $serializer;
        $this->commandManager = $commandManager;
        $this->commandInterpreter = $commandInterpreter;
        parent::__construct($container);
    }

    public function listAction()
    {
        return $this->commandManager->getCommandsConfigs();
    }

    public function sendAction()
    {
        $receivedCommand = new CommandSource();
        $form = $this->container->getFormFactory()->create(new CommandType(), $receivedCommand);

        if ('POST' === $this->container->getRequest()->getMethod()) {
            $form->bind($this->container->getRequest());

            $em = $this->container->getDoctrine()->getManager();
            $commandsResults = $this->commandInterpreter->interpret($receivedCommand);
            $this->setCommandsErrors($commandsResults);
            $em->flush();

            return $commandsResults;
        }

        return $this->view([
            'form'  => $form->createView(),
        ]);
    }

    public function formAction()
    {
        $form = $this->container->getFormFactory()->create(new CommandType(), new CommandSource());
        return $this->view([
            'form' => $form->createView(),
        ]);
    }

    /**
     * Sets the default error message when a command does not return any result,
     * which means it doesn't know how to handle a request.
     *
     * @param $commandsResults
     */
    private function setCommandsErrors(&$commandsResults)
    {
        $iteratedCommandsResults = $commandsResults;
        foreach ($iteratedCommandsResults as $command => $commandResults) {
            foreach ($commandResults as $commandNumber => $commandResult) {
                if (empty($commandResult)) {
                    $commandsResults[$command][$commandNumber]['error'] = $this->renderView('EntvalleyAppBundle:Command:executed/_failed.html.twig', [
                        'command' => $command,
                        'command_config' => $this->commandManager->getCommandConfig($command)
                    ]);
                }
            }
        }
    }
}
