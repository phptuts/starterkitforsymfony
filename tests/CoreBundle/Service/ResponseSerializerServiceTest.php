<?php

namespace Tests\CoreBundle\Service;

use CoreBundle\Entity\User;
use CoreBundle\Model\Response\ResponseModel;
use CoreBundle\Service\ResponseSerializerService;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

class ResponseSerializerServiceTest extends BaseTestCase
{
    /**
     * @var Serializer|Mock
     */
    protected $serializer;

    /**
     * @var ResponseSerializerService
     */
    protected $responseSerializerService;

    protected function setUp()
    {
        parent::setUp();
        $this->serializer = \Mockery::mock(Serializer::class);
        $this->responseSerializerService = new ResponseSerializerService($this->serializer);
    }

    public function testCreatingSerializeResponse()
    {
        $responseModel = new ResponseModel(new User());


        $this->serializer->shouldReceive('serialize')->with(
            $responseModel->getBody(),
            'json',
            \Mockery::type(SerializationContext::class)
        )->andReturn(json_encode(['data']));

        $jsonResponse = $this->responseSerializerService->serializeResponse($responseModel, ['users'], 201);

        Assert::assertEquals($jsonResponse->getContent(), json_encode(['data']));
    }
}