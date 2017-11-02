<?php


namespace Test\AppBundle\Security\Provider;

use AppBundle\Entity\RefreshToken;
use AppBundle\Entity\User;
use AppBundle\Exception\ProgrammerException;
use AppBundle\Repository\RefreshTokenRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Security\Provider\RefreshTokenProvider;
use AppBundle\Service\Credential\RefreshTokenService;
use AppBundle\Service\User\UserService;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Tests\BaseTestCase;

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