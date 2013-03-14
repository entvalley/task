<?php

namespace Entvalley\AppBundle\EventListener;

use Entvalley\AppBundle\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ApiListener
{
    private $supportedFormats;
    private $jsonSerializer;
    private $serializationContext;

    public function __construct(array $supportedFormats, $jsonSerializer, SerializationContext $serializationContext)
    {
        if (empty($supportedFormats)) {
            throw new \RuntimeException("You must set at least one supported format");
        }
        $this->supportedFormats = $supportedFormats;
        $this->jsonSerializer = $jsonSerializer;
        $this->serializationContext = $serializationContext;
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $acceptableTypes = $request->getAcceptableContentTypes();

        $responseFormat = null;

        foreach ($acceptableTypes as $acceptableType) {
            if (in_array($request->getFormat($acceptableType), $this->supportedFormats)) {
                $responseFormat = $request->getFormat($acceptableType);
                break;
            }
        }

        if ($responseFormat === null && in_array('*/*', $acceptableTypes)) {
            $responseFormat = $this->supportedFormats[0];
        }

        $response = null;
        if ($responseFormat) {
            switch ($responseFormat) {
                case "json":
                    $response = JsonResponse::createWithSerializer($this->jsonSerializer, $event->getControllerResult(), $this->serializationContext);
                    break;
            }
        }

        if (!$response) {
            $response = new Response('No supported format is found (did you send a correct "Accept" header?)', 415);
        }

        $event->setResponse($response);
    }
}
