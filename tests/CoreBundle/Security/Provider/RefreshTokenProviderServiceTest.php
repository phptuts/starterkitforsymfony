<?php


namespace Test\CoreBundle\Security\Provider;

use CoreBundle\Entity\RefreshToken;
use CoreBundle\Exception\ProgrammerException;
use CoreBundle\Repository\RefreshTokenRepository;
use CoreBundle\Repository\UserRepository;
use CoreBundle\Security\Provider\RefreshTokenProvider;
use CoreBundle\Service\Credential\RefreshTokenService;
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
     * @var UserRepository|Mock
     */
    protected $userRepository;

    /**
     * @var RefreshTokenService|Mock
     */
    protected $refreshTokenService;

    /**
     * @var RefreshTokenRepository|Mock
     */
    protected $refreshTokenRepository;

    public function setUp()
    {
        parent::setUp();
        $this->userRepository = \Mockery::mock(UserRepository::class);
        $this->refreshTokenService = \Mockery::mock(RefreshTokenService::class);
        $this->refreshTokenRepository = \Mockery::mock(RefreshTokenRepository::class);

        $this->refreshTokenProvider =
            new RefreshTokenProvider($this->userRepository,$this->refreshTokenRepository, $this->refreshTokenService);
    }

    /**
     * Tests taht a valid refresh token can find a user.  Once the user is we test that token is marked used so it can not be used again.
     */
    public function testValidRefreshTokenFound()
    {
        $refreshToken = new RefreshToken();
        Assert::assertFalse($refreshToken->isUsed());

        $this->refreshTokenRepository->shouldReceive('getValidRefreshToken')->with('token')->andReturn($refreshToken);
        $this->refreshTokenService->shouldReceive('save')->with($refreshToken);

        $this->refreshTokenProvider->loadUserByUsername('token');

        Assert::assertTrue($refreshToken->isUsed());
    }

    /**
     * test that if a valid refresh token is not found a UsernameNotFoundException is thrown
     */
    public function testRefreshTokenNotFound()
    {
        $this->expectException(UsernameNotFoundException::class);
        $refreshToken = new RefreshToken();
        Assert::assertFalse($refreshToken->isUsed());

        $this->refreshTokenRepository->shouldReceive('getValidRefreshToken')->with('token')->andReturnNull();
        $this->refreshTokenProvider->loadUserByUsername('token');
    }

    /**
     * Tests that if duplicate refresh tokens our found that the special
     * exception code and message are thrown in the UsernameNotFoundException.
     */
    public function testDuplicateRefreshToken()
    {
        $this->expectException(UsernameNotFoundException::class);
        $this->expectExceptionCode(ProgrammerException::REFRESH_TOKEN_DUPLICATE);
        $this->expectExceptionMessage('Duplicate Refresh Token.');
        $refreshToken = new RefreshToken();
        Assert::assertFalse($refreshToken->isUsed());

        $this->refreshTokenRepository
            ->shouldReceive('getValidRefreshToken')
            ->with('token')
            ->andThrow(new ProgrammerException('Duplicate Refresh Token.', ProgrammerException::REFRESH_TOKEN_DUPLICATE));
        $this->refreshTokenProvider->loadUserByUsername('token');
    }

}