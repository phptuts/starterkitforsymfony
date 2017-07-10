<?php


namespace Tests\CoreBundle\Service\Credential;


use CoreBundle\Entity\User;
use CoreBundle\Model\Response\ResponseModel;
use CoreBundle\Model\Security\AuthTokenModel;
use CoreBundle\Model\Security\CredentialModel;
use CoreBundle\Service\Credential\CredentialModelBuilderService;
use CoreBundle\Service\Credential\CredentialResponseBuilderService;
use CoreBundle\Service\ResponseSerializerService;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tests\BaseTestCase;

class CredentialResponseBuilderServiceTest extends BaseTestCase
{
    /**
     * @var CredentialResponseBuilderService
     */
    protected $credentialResponseBuilderService;

    /**
     * @var ResponseSerializerService|Mock
     */
    protected $responseSerializerService;

    /**
     * @var CredentialModelBuilderService|Mock
     */
    protected $credentialModelBuilderService;

    public function setUp()
    {
        parent::setUp();
        $this->credentialModelBuilderService = \Mockery::mock(CredentialModelBuilderService::class);
        $this->responseSerializerService = \Mockery::mock(ResponseSerializerService::class);
        $this->credentialResponseBuilderService = new CredentialResponseBuilderService($this->credentialModelBuilderService, $this->responseSerializerService);
    }

    public function testCreateCredentialResponse()
    {
        $user = new User();
        $credModel = new CredentialModel($user, new AuthTokenModel('token', 33), new AuthTokenModel('refresh_token', 133));

        $jsonResponse = \Mockery::mock(JsonResponse::class);

        $this->credentialModelBuilderService->shouldReceive('createCredentialModel')->with($user)->andReturn($credModel);
        $this->responseSerializerService->shouldReceive('serializeResponse')
                ->with(\Mockery::on(function (ResponseModel $responseModel) use($credModel) {
                    return $responseModel->getBody()['data']->getUser() == $credModel->getUser();
            }), ['users'], 201)->andReturn($jsonResponse);

        $returnResponse = $this->credentialResponseBuilderService->createCredentialResponse($user);


        Assert::assertEquals($jsonResponse, $returnResponse);
    }
}