<?php

namespace Tests\ApiBundle\Controller\User;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\ApiBundle\Controller\RequestTrait;

class RegisterTest extends WebTestCase
{
    use RequestTrait;

    public function testValidation()
    {
        $client = $this->makeClient();
        $response = $this->makeJsonRequest($client, Request::METHOD_POST, '/api/users', []);
        $json = $this->getJsonResponse($response);

        Assert::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        Assert::assertNotEmpty($json['data']['children']['email']['errors']);
        Assert::assertNotEmpty($json['data']['children']['plainPassword']['errors']);
        Assert::assertEquals('formErrors',$json['meta']['type']);
        Assert::assertFalse($json['meta']['paginated']);
    }


}