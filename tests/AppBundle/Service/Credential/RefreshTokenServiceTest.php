<?php


namespace Tests\AppBundle\Service\Credential;


use AppBundle\Entity\RefreshToken;
use AppBundle\Entity\User;
use AppBundle\Exception\ProgrammerException;
use AppBundle\Repository\RefreshTokenRepository;
use AppBundle\Service\Credential\RefreshTokenService;
use Doctrine\ORM\EntityManager;
use Mockery\Mock;
use PHPUnit\Framework\Assert;
use Tests\BaseTestCase;

class RefreshTokenServiceTest extends BaseTestCase
{
    /**
     * @var EntityManager|Mock
     */
    protected $em;


    /**
     * @var RefreshTokenRepository|Mock
     */
    protected $refreshTokenRepo;

    /**
     * @var RefreshTokenService
     */
    protected $refreshTokenService;

    public function setUp()
    {
        $this->em = \Mockery::mock(EntityManager::class);
        $this->refreshTokenRepo = $this->getContainer()->get('doctrine')->getRepository(RefreshToken::class);
        $this->refreshTokenService = new RefreshTokenService($this->em, $this->refreshTokenRepo, 1000);
    }

    /**
     * Tests that create refresh token saves the correct expiration date, token and that the token is not used
     * Basically making sure everything is valid
     */
    public function testCreateRefreshToken()
    {
        $lessThanExpirationTimeStamp = (new \DateTime())->modify('+' . (1000 - 500) .  ' seconds')->getTimestamp();
        $greaterThanExpirationTimeStamp = (new \DateTime())->modify('+' . (1000 + 500) .  ' seconds')->getTimestamp();

        $user = new User();
        $this->em->shouldReceive('persist')->once()->with(\Mockery::type(RefreshToken::class));
        $this->em->shouldReceive('flush')->once();

        $refreshToken =  $this->refreshTokenService->createRefreshToken($user);

        Assert::assertEquals($user, $refreshToken->getUser());
        Assert::assertTrue($lessThanExpirationTimeStamp < $refreshToken->getExpires()->getTimestamp());
        Assert::assertTrue($greaterThanExpirationTimeStamp > $refreshToken->getExpires()->getTimestamp());
        Assert::assertFalse($refreshToken->isUsed());
        Assert::assertNotNull($refreshToken->getToken());
    }

    /**
     * Tests that if their are duplicate valid refresh tokens a special expection is thrown
     */
    public function testDuplicateRefreshToken()
    {
        $this->expectException(ProgrammerException::class);
        $this->expectExceptionCode(ProgrammerException::REFRESH_TOKEN_DUPLICATE);

        $this->refreshTokenService->getValidRefreshToken('token_dup');
    }

    /**
     * Tests that expired refresh token are not valid
     */
    public function testExpiredToken()
    {
        Assert::assertNull($this->refreshTokenService->getValidRefreshToken('token_expired'));
    }

    /**
     * Tests that used refresh tokens are not valid
     */
    public function testUsedToken()
    {
        Assert::assertNull($this->refreshTokenService->getValidRefreshToken('used_token'));
    }

    /**
     * Tests that the method can find a valid refresh token
     */
    public function testValidToken()
    {
        Assert::assertInstanceOf(RefreshToken::class, $this->refreshTokenService->getValidRefreshToken('valid_refresh_token'));
    }
}