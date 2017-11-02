<?php


namespace Tests\AppBundle\Service\Credential;


use AppBundle\Entity\User;
use AppBundle\Model\Security\AuthTokenModel;
use AppBundle\Service\Credential\CredentialModelBuilderService;
use AppBundle\Service\Credential\JWSService;
use AppBundle\Service\User\UserService;
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
     * @var UserService|Mock
     */
    protected $userService;

    /**
     * @var JWSService|Mock
     */
    protected $jwsTokenService;

    public function setUp()
    {
        parent::setUp();
        $this->userService = \Mockery::mock(UserService::class);
        $this->jwsTokenService = \Mockery::mock(JWSService::class);
        $this->credentialModelBuilderService = new CredentialModelBuilderService($this->jwsTokenService, $this->userService);
    }

    /**
     * Tests the insides of a Credential Model to make sure everything is set
     */
    public function testCreateCredentialModel()
    {
        $user = new User();
        $user->setRefreshTokenExpire(new \DateTime())->setRefreshToken('refresh_token');
        $tokenModel = new AuthTokenModel('token', 22);

        $this->jwsTokenService->shouldReceive('createAuthTokenModel')->with($user)->andReturn($tokenModel);
        $this->userService->shouldReceive('updateUserRefreshToken')->with($user)->andReturn($user);

        $credModel = $this->credentialModelBuilderService->createCredentialModel($user);

        Assert::assertEquals($tokenModel, $credModel->getTokenModel());
        Assert::assertEquals(
            $user->getAuthRefreshModel()->getExpirationTimeStamp(),
            $credModel->getRefreshTokenModel()->getExpirationTimeStamp());
        Assert::assertEquals(
            $user->getRefreshToken(),
            $credModel->getRefreshTokenModel()->getToken()
        );
        Assert::assertEquals($user, $credModel->getUser());
    }
}