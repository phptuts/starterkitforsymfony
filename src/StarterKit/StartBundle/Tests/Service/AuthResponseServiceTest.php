<?php

namespace StarterKit\StartBundle\Tests\Service;

use Mockery\Mock;
use PHPUnit\Framework\Assert;
use StarterKit\StartBundle\Model\Auth\AuthTokenModel;
use StarterKit\StartBundle\Service\AuthResponseService;
use StarterKit\StartBundle\Service\AuthTokenService;
use StarterKit\StartBundle\Service\UserService;
use StarterKit\StartBundle\Tests\BaseTestCase;
use StarterKit\StartBundle\Tests\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class AuthResponseServiceTest extends BaseTestCase
{
    /**
     * @var UserService|Mock
     */
    protected $userService;

    /**
     * @var AuthTokenService|Mock
     */
    protected $jwsService;

    /**
     * @var AuthResponseService
     */
    protected $authResponseService;

    public function setUp()
    {
        parent::setUp();
        $this->jwsService = \Mockery::mock(AuthTokenService::class);
        $this->userService = \Mockery::mock(UserService::class);
        $this->authResponseService = new AuthResponseService($this->jwsService, $this->userService);
    }

    /**
     * Tests that a auth response is created correctly
     */
    public function testCreateAuthResponse()
    {
        $expireRefreshToken = new \DateTime();
        $user = new User();
        $user->setRefreshToken('refresh_token')->setRefreshTokenExpire($expireRefreshToken);
        $jwtModel = new AuthTokenModel('jwt_token', 333);
        $this->userService->shouldReceive('updateUserRefreshToken')->with($user)->once()->andReturn($user);
        $this->jwsService->shouldReceive('createAuthTokenModel')->with($user)->once()->andReturn($jwtModel);

        $response = $this->authResponseService->createAuthResponse($user);

        $json = json_decode($response->getContent(), true);

        Assert::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        Assert::assertEquals($json['meta']['type'], 'authentication');
        Assert::assertFalse($json['meta']['paginated']);
        Assert::assertEquals($json['data']['user'], $user->singleView());

        Assert::assertEquals($json['data']['tokenModel']['token'], 'jwt_token');
        Assert::assertEquals($json['data']['tokenModel']['expirationTimeStamp'], 333);

        Assert::assertEquals($json['data']['refreshTokenModel']['token'], 'refresh_token');
        Assert::assertEquals($json['data']['refreshTokenModel']['expirationTimeStamp'], $expireRefreshToken->getTimestamp());


    }
}
