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

    private function _prepareCommandsResponse($commandsResults)
    {
        $finalResponse = '';

        foreach ($commandsResults as $command => $commandResults) {
            foreach ($commandResults as $commandResult) {
                foreach ($commandResult as $commandResultKey => $commandResultItem) {
                    /**
                     * Serializing object requires complicated logic
                     */
                 //  $commandResult[$commandResultKey] = is_object($commandResultItem) ? $this->serializer->serialize($commandResultItem, 'json') : JsonEncoder::encode($commandResultItem);
                }

                if (empty($commandResult)) {
                    $finalResponse .= $this->renderView('EntvalleyAppBundle:Command:executed/_failed.html.twig', [
                        'command' => $command,
                        'command_config' => $this->commandManager->getCommandConfig($command)
                    ]);
                } else {
                    $finalResponse .= $this->renderView('EntvalleyAppBundle:Command:executed/' . $command . '.html.twig', $commandResult);
                }
            }
        }
        return $finalResponse;
    }
}
