<?php

namespace Entvalley\AppBundle\Controller;

use Entvalley\AppBundle\Domain\JsonEncoder;
use Entvalley\AppBundle\Domain\Command\CommandInterpreter;
use Symfony\Component\HttpFoundation\Request;
use Entvalley\AppBundle\Domain\Command\CommandManager;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use JMS\SerializerBundle\Serializer\SerializerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Entvalley\AppBundle\Form\CommandType;
use Entvalley\AppBundle\Domain\Command\CommandSource;
use Symfony\Component\HttpFoundation\JsonResponse;

class CommandController extends Controller
{
    private $serializer;
    private $commandManager;
    private $commandInterpreter;

    public function __construct(Request $request,
        RouterInterface $router,
        $templating,
        SessionInterface $session,
        RegistryInterface $doctrine,
        FormFactoryInterface $formFactory,
        SerializerInterface $serializer,
        CommandManager $commandManager,
        CommandInterpreter $commandInterpreter)
    {
        $this->serializer = $serializer;
        $this->commandManager = $commandManager;
        $this->commandInterpreter = $commandInterpreter;
        parent::__construct($request, $router, $templating, $session, $doctrine, $formFactory);
    }

    public function listAction()
    {
        return new JsonResponse($this->commandManager->getCommandsConfigs());
    }

    public function sendAction()
    {
        $receivedCommand = new CommandSource();
        $form = $this->formFactory->create(new CommandType(), $receivedCommand);

        if ('POST' === $this->request->getMethod()) {
            $form->bind($this->request);

            $em = $this->doctrine->getManager();
            $commandsResults = $this->commandInterpreter->interpret($receivedCommand);
            $em->flush();

            return $this->createResponse($this->_prepareCommandsResponse($commandsResults));
        }

        return $this->view([
            'form'  => $form->createView(),
        ]);
    }

    public function formAction()
    {
        $form = $this->formFactory->create(new CommandType(), new CommandSource());
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
                    $commandResult[$commandResultKey] = is_object($commandResultItem) ? $this->serializer->serialize($commandResultItem, 'json') : JsonEncoder::encode($commandResultItem);
                }

                if (empty($commandResult)) {
                    $finalResponse .= $this->renderView('EntvalleyAppBundle:Command:executed/_failed.html.twig', [
                        'command' => $command
                    ]);
                } else {
                    $finalResponse .= $this->renderView('EntvalleyAppBundle:Command:executed/' . $command . '.html.twig', $commandResult);
                }
            }
        }
        return $finalResponse;
    }
}
