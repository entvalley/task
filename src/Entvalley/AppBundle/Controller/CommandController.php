<?php

namespace Entvalley\AppBundle\Controller;

use Mzz\MzzBundle\Controller\Controller;
use Entvalley\AppBundle\Domain\JsonEncoder;
use Entvalley\AppBundle\Form\CommandType;
use Entvalley\AppBundle\Domain\Command\CommandSource;
use Symfony\Component\HttpFoundation\JsonResponse;
use Entvalley\AppBundle\Entity\User;

class CommandController extends Controller
{
    public function listAction()
    {
        $commandManager = $this->get('entvalley.command_manager');

        return new JsonResponse($commandManager->getCommandNames());
    }

    public function sendAction()
    {
        $receivedCommand = new CommandSource();
        $form = $this->createForm(new CommandType(), $receivedCommand);

        $request = $this->getRequest();

        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            $em = $this->getDoctrine()->getManager();
            $interpreter = $this->get("entvalley.command_interpreter");
            $commandsResults = $interpreter->interpret($receivedCommand);
            $em->flush();

            return $this->createResponse($this->_prepareCommandsResponse($commandsResults));
        }

        return $this->view(array(
            'form'  => $form->createView(),
        ));
    }

    public function formAction()
    {
        $form = $this->createForm(new CommandType(), new CommandSource());
        return $this->view(array(
            'form' => $form->createView(),
        ));
    }

    private function _prepareCommandsResponse($commandsResults)
    {
        $finalResponse = '';
        $serializer = $this->get('serializer');

        foreach ($commandsResults as $command => $commandResults) {
            foreach ($commandResults as $commandResult) {
                foreach ($commandResult as $commandResultKey => $commandResultItem) {
                    /**
                     * Serializing object requires complicated logic
                     */
                    $commandResult[$commandResultKey] = is_object($commandResultItem) ? $serializer->serialize($commandResultItem, 'json') : JsonEncoder::encode($commandResultItem);
                }
                $finalResponse .= $this->renderView('EntvalleyAppBundle:Command:executed/' . $command . '.html.twig', $commandResult);
            }
        }
        return $finalResponse;
    }
}
