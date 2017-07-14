<?php

namespace Tests\ApiBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

trait RequestTrait
{
    /**
     * @param Client $client
     * @param string $method
     * @param string $url
     * @param array $data
     * @param string $authToken
     *
     * @return Response
     */
    public function makeJsonRequest(Client $client, $method, $url, $data, $authToken = null)
    {
        $headers = ['HTTP_Content-type' => 'application/json'];

        if (empty($authToken)) {
            $headers['HTTP_Authorization'] = $authToken;
        }

        $client->request($method, $url, $data, [], $headers);

        return $client->getResponse();
    }

    public function getJsonResponse(Response $response)
    {
        return json_decode($response->getContent(), true);
    }
}