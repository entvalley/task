<?php

namespace Entvalley\AppBundle\Http;

class Client
{
    private $connection;

    protected function tryToConnect($host, $port)
    {
        if ($port === 443) {
            $transport = 'ssl';
        } else {
            $transport = 'tcp';
        }
        $this->connection = stream_socket_client("$transport://$host:$port", $errno, $errstr);

        if (!$this->connection) {
            throw new \RuntimeException("Unable to connect to $host:$port");
        }
    }

    public function request($method, $url, $data = null, $headers = array())
    {
        list($host, $path, $port) = $this->parseUrl($url);

        $this->tryToConnect($host, $port);
        $request = $this->buildRequest($method, $path, $host, $headers, $data);
        $this->writeRequest($request);

        $responseHeaders = $this->readResponseHeaders();

        // handle redirects
        switch ($responseHeaders['http_status']) {
            case 301:
            case 302:
            case 303:
            case 307:
                return $this->request($method, $responseHeaders['location'], $data, $headers);
        }
        $body = $this->readBody($responseHeaders);

        $this->disconnect();

        return new Response($body, $responseHeaders, $responseHeaders['http_status']);
    }

    /**
     * @param $url
     * @return array
     */
    private function parseUrl($url)
    {
        $urlParts = parse_url($url);
        $path = $urlParts['path'] . (!empty($urlParts['query']) ? '?' . $urlParts['query'] : '');

        if (isset($urlParts['port'])) {
            $port = $urlParts['port'];
        } else {
            $port = $urlParts['scheme'] === 'http' ? 80 : 443;
        }
        return array($urlParts['host'], $path, $port);
    }

    private function readBody($responseHeaders)
    {
        $body = '';
        if (isset($responseHeaders['transfer-encoding']) && $responseHeaders['transfer-encoding'] === 'chunked') {
            do {
                $chunkMeta = trim(fgets($this->connection));

                if (preg_match('~(?P<length>[[:xdigit:]]+)(?:;.*)?$~', $chunkMeta, $matches)) {
                    $chunkLength = hexdec($matches['length']);
                    $bytesToRead = $chunkLength;

                    while ($bytesToRead > 0) {
                        $buffer = fread($this->connection, $bytesToRead);
                        $bytesToRead -= strlen($buffer);
                        $body .= $buffer;
                    }

                    // ending CRLF
                    if ($bytesToRead === 0) {
                        fread($this->connection, 2);
                    }
                } else {
                    $chunkLength = 0;
                }

            } while ($chunkLength);

        } else {
            $bytesToRead = isset($responseHeaders['content-length']) ? (int)$responseHeaders['content-length'] : 0;
            while ($bytesToRead > 0) {
                $buffer = fgets($this->connection, $bytesToRead + 1);
                $bytesToRead -= strlen($buffer);
                $body .= $buffer;
            }
        }
        return $body;
    }

    private function readResponseHeaders()
    {
        $headers = array();
        while (($rawLine = fgets($this->connection)) !== false && ($line = trim($rawLine)) !== '') {
            if (preg_match('~^HTTP/(?P<version>\d\.\d)\s+(?P<status>\d+)~', $line, $matches)) {
                $headers['http_status'] = $matches['status'];
                $headers['http_version'] = $matches['version'];
            } else {
                list($header_name, $header_value) = explode(':', $line, 2);
                $headers[strtolower($header_name)] = trim($header_value);
            }
        }
        return $headers;
    }

    private function buildRequest($method, $path, $host, $headers, $data)
    {
        $request = strtoupper($method) . " $path HTTP/1.1\r\nHost: $host\r\n";
        $request .= "Connection: close\r\n";
        foreach ($headers as $header => $value) {
            $request .= $header . ": " . $value . "\r\n";
        }
        $request .= "\r\n";

        if ($data !== null) {
            $request .= $data;
        }
        return $request;
    }

    private function writeRequest($request)
    {
        return fwrite($this->connection, $request);
    }

    private function disconnect()
    {
        return fclose($this->connection);
    }
}