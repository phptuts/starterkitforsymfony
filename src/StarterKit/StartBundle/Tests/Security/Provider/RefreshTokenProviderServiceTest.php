<?php


namespace StarterKit\StartBundle\Security\Provider;

use Mockery\Mock;
use StarterKit\StartBundle\Security\Provider\RefreshTokenProvider;
use StarterKit\StartBundle\Service\UserService;
use StarterKit\StartBundle\Tests\BaseTestCase;
use StarterKit\StartBundle\Tests\Entity\User;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class RefreshTokenProviderServiceTest extends BaseTestCase
{
    /**
     * @var RefreshTokenProvider|Mock
     */
    protected $refreshTokenProvider;

    /**
     * @var UserService|Mock
     */
    protected $userService;



    public function setUp()
    {
        parent::setUp();
        $this->userService = \Mockery::mock(UserService::class);

        $this->refreshTokenProvider =
            new RefreshTokenProvider($this->userService);
    }

    /**
     * Tests that a valid refresh token can find a user.  That it update the refresh token
     */
    public function testValidRefreshTokenFound()
    {
        $user = new User();
        $this->userService
            ->shouldReceive('findUserByValidRefreshToken')
            ->once()
            ->with('refresh_token')
            ->andReturn($user);
        $this->userService->shouldReceive('updateUserRefreshToken')->once()->with($user);
        $this->refreshTokenProvider->loadUserByUsername('refresh_token');
    }

    /**
     * test that if a valid refresh token is not found a UsernameNotFoundException is thrown
     */
    public function testRefreshTokenNotFound()
    {
        $this->expectException(UsernameNotFoundException::class);

        $this->userService
            ->shouldReceive('findUserByValidRefreshToken')
            ->once()
            ->with('refresh_token')
            ->andReturn(null);

        $this->refreshTokenProvider->loadUserByUsername('refresh_token');

    }


}