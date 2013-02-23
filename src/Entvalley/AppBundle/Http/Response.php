<?php

namespace Entvalley\AppBundle\Http;

class Response
{
    private $status;
    private $headers;
    private $body;

    public function __construct($body, $headers, $status)
    {
        $this->body = $body;
        $this->headers = $headers;
        $this->status = $status;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
