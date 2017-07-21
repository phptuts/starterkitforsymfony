<?php

namespace Tests\AppBundle\Controller\Api;

use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExceptionTest extends BaseApiTestCase
{
    /**
     *  This how an error response should look like.
     *  {
     *      "meta": {
     *          "exceptionCode": 10233243, // this is the code in the exception
     *          "type": "exception",
     *          "lookupCode": "151500176089-59", //
     *          "instance": "AppBundle\\Exception\\ProgrammerException"
     *      },
     *      "data": {
     *          "message": "I am a stupid exception."
     *      }
     *  }
     *
     * Tests that all the metadata is set in the response along with the message.
     */
    public function testStupidException()
    {
        $client = $this->makeClient();
        $response = $this->makeJsonRequest($client, Request::METHOD_GET, '/api/stupid_exception', []);

        Assert::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        $json = $this->getJsonResponse($response);
        Assert::assertArrayHasKey('meta', $json);
        Assert::assertArrayHasKey('data', $json);

        $meta = $json['meta'];
        Assert::assertEquals(10233243, $meta['exceptionCode']);
        Assert::assertEquals('exception', $meta['type']);
        Assert::assertEquals('exception', $meta['type']);
        Assert::assertNotEmpty($meta['lookupCode']);
        Assert::assertEquals('AppBundle\\Exception\\ProgrammerException', $meta['instance']);

        Assert::assertEquals('I am a stupid exception.', $json['data']['message']);
    }
}

