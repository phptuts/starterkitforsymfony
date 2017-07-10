<?php


namespace Tests\CoreBundle\Service\Credential;


use CoreBundle\Entity\RefreshToken;
use CoreBundle\Entity\User;
use CoreBundle\Model\Security\AuthTokenModel;
use CoreBundle\Service\Credential\CredentialModelBuilderService;
use CoreBundle\Service\Credential\JWSService;
use CoreBundle\Service\Credential\RefreshTokenService;
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