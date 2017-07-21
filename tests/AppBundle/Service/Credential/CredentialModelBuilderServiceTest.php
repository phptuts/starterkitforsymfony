<?php


namespace Tests\AppBundle\Service\Credential;


use AppBundle\Entity\RefreshToken;
use AppBundle\Entity\User;
use AppBundle\Model\Security\AuthTokenModel;
use AppBundle\Service\Credential\CredentialModelBuilderService;
use AppBundle\Service\Credential\JWSService;
use AppBundle\Service\Credential\RefreshTokenService;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

class CredentialModelBuilderServiceTest extends BaseTestCase
{
    /**
     * @var CredentialModelBuilderService
     */
    protected $credentialModelBuilderService;

    /**
     * @var RefreshTokenService|Mock
     */
    protected $refreshTokenService;

    /**
     * @var JWSService|Mock
     */
    protected $jwsTokenService;

    public function setUp()
    {
        parent::setUp();
        $this->refreshTokenService = \Mockery::mock(RefreshTokenService::class);
        $this->jwsTokenService = \Mockery::mock(JWSService::class);
        $this->credentialModelBuilderService = new CredentialModelBuilderService($this->jwsTokenService, $this->refreshTokenService);
    }

    /**
     * Tests the insides of a Credential Model to make sure everything is set
     */
    public function testCreateCredentialModel()
    {
        $user = new User();
        $tokenModel = new AuthTokenModel('token', 22);

        $expires = new \DateTime();

        $refreshToken = (new RefreshToken())->setToken('refresh_token')->setExpires($expires);

        $this->jwsTokenService->shouldReceive('createAuthTokenModel')->with($user)->andReturn($tokenModel);
        $this->refreshTokenService->shouldReceive('createRefreshToken')->with($user)->andReturn($refreshToken);

        $credModel = $this->credentialModelBuilderService->createCredentialModel($user);

        Assert::assertEquals($tokenModel, $credModel->getTokenModel());
        Assert::assertEquals($refreshToken->getExpires()->getTimestamp(), $credModel->getRefreshTokenModel()->getExpirationTimeStamp());
        Assert::assertEquals($refreshToken->getToken(), $credModel->getRefreshTokenModel()->getToken());
        Assert::assertEquals($user, $credModel->getUser());
    }
}