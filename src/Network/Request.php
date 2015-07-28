<?php

namespace djekl\Currency\Network;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;

class Request
{
    protected $client;
    protected $ip;
    protected $ip_headers;

    public function __construct()
    {
        $this->client = new Client([
            "strict" => false,
            "future" => true,
        ]);

        $this->client->setDefaultOption("timeout", 30);
        $this->client->setDefaultOption("connect_timeout", 30);
        $this->client->setDefaultOption("allow_redirects", true);
        $this->client->setDefaultOption("verify", false);

        $this->setIpHeaders();
    }

    public function fetch($url)
    {
        $method = "GET";
        $request = $this->client->createRequest($method, $url);
        $this->data["headers"] = $this->ip_headers;
        $headers = $this->ip_headers;

        foreach ($headers as $header) {
            $header = explode(": ", $header);
            $this->data["headers"][$header[0]] = $header[1];
        }

        $request->setHeaders($this->data["headers"]);

        try {
            $response = $this->client->send($request);
        } catch (ClientException $e) {
            $response = $this->extractExceptionInfo($e);
        } catch (RequestException $e) {
            $response = $this->extractExceptionInfo($e);
        } catch (ServerException $e) {
            $response = $this->extractExceptionInfo($e);
        }

        $this->returnData = [
            "request"  => [
                "url" => $url,
            ],
            "response" => [
                "statusCode" => $response->getStatusCode(),
                "statusText" => $response->getReasonPhrase(),
                "headers" => $response->getHeaders(),
                "data" => (string)$response->getBody(),
                "url" => $response->getEffectiveUrl(),
                "raw" => (string)$response,
            ],
        ];

        return $this->returnData;
    }

    protected function extractExceptionInfo($e)
    {
        if ($e->hasResponse()) {
            return $e->getResponse();
        }

        return json_encode([
            "error" => $e->getMessage(),
        ], JSON_PRETTY_PRINT);
    }

    protected function setIp()
    {
        $this->ip = rand(60, 80) . '.' . rand(60, 140) . '.' . rand(80, 120) . '.' . rand(120, 200);

        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $this->ip = $_SERVER['REMOTE_ADDR'];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }

    protected function setIpHeaders()
    {
        $this->setIp();
        $this->ip_headers = [
            "REMOTE_ADDR: {$this->ip}",
            "HTTP_X_FORWARDED_FOR: {$this->ip}",
        ];
    }
}
