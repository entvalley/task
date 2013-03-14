<?php

namespace Entvalley\AppBundle\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\JsonResponse as BaseJsonResponse;
use JMS\Serializer\SerializerInterface;


class JsonResponse extends BaseJsonResponse
{
    public static function createWithSerializer(SerializerInterface $serializer, $data = array(), $context = null, $status = 200, $headers = array())
    {
        $response = new static(array(), $status, $headers);
        $response->setJsonData($serializer->serialize($data, 'json', $context));
        return $response;
    }

    /**
     * Sets the raw data to be sent as json (the data should be already in json format)
     *
     * @param string $data
     *
     * @return JsonResponse
     */
    protected function setJsonData($data)
    {
        if (!is_string($data)) {
            throw new \RuntimeException('Only JSON strings are allowed.');
        }

        $this->data = $data;

        return $this->update();
    }
}
