<?php

namespace App\Service;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ClientRequestService
{
    CONST GET = 'GET';
    CONST POST = 'POST';

    private $base_url;
    private $header;
    private $client;
    private $token;

    /**
     * @param array $options
     * @return $this
     */
    public function setting( Array $options) {

        $this->base_url = $options['base_url'];
        $this->header = $options['header'];
        $this->client = new Client([
            'base_uri' => $this->base_url,
            ['headers' => $this->header],
        ]);

        return $this;
    }

    /**
     * @param string $tokenUri
     * @param array $options
     */
    public function setToken(string $tokenUri, array $options) {
        $response = $this->post($tokenUri, $options);

        $this->token = json_decode($response->getContent())->message;
    }

    /**
     * @return JsonResponse
     */
    public function getToken() {
        return $this->prettyJson(200, $this->token);
    }

    /**
     * @param string $uri
     * @param array $options
     * @return JsonResponse
     */
    public function get(string $uri, array $options) {
        return $this->getReponse(self::GET, $uri, $options);
    }

    /**
     * @param string $uri
     * @param array $options
     * @return JsonResponse
     */
    public function post(string $uri, array $options) {
        return $this->getReponse(self::POST, $uri, $options);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return JsonResponse
     */
    private function getReponse(string $method, string $uri, array $options ) {
        $response = $this->client->request($method, $uri, $options);

        return $this->getStatusAndContent($response);
    }

    /**
     * @param \GuzzleHttp\Psr7\Response $response
     * @return JsonResponse
     */
    private function getStatusAndContent(\GuzzleHttp\Psr7\Response $response) {
        switch ($response->getStatusCode()) {
            case 200:
                $message = json_decode($response->getBody()->getContents());
                break;
            case 400:
                $message = 'Bad Request';
                break;
            case 401:
                $message = 'Unauthorized';
                break;
            case 403:
                $message = 'Forbidden';
                break;
            case 404:
                $message = 'Not Found';
                break;
            case 405:
                $message = 'Method Not Allowed';
                break;
            case 500:
                $message = 'Internal Server Error';
                break;
            case 503:
                $message = 'Service Unavailable';
                break;
            case 504:
                $message = 'Gateway Time-out';
                break;
            default:
                $message = Response::$statusTexts[$response->getStatusCode()];
                break;
        }

        return $this->prettyJson($response->getStatusCode(), $message);
    }

    /**
     * @param int $code
     * @param $message
     * @return JsonResponse
     */
    private function prettyJson(int $code, $message) {
        $jsonResponse = new JsonResponse([
            'status' => $code,
            'message' => $message
        ]);

        return $jsonResponse->setEncodingOptions($jsonResponse->getEncodingOptions() | JSON_PRETTY_PRINT);
    }
}