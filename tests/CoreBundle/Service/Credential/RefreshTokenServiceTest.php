<?php


namespace Tests\CoreBundle\Service\Credential;


use CoreBundle\Entity\RefreshToken;
use CoreBundle\Entity\User;
use CoreBundle\Repository\RefreshTokenRepository;
use CoreBundle\Service\Credential\RefreshTokenService;
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
        $this->refreshTokenRepo = \Mockery::mock(RefreshTokenRepository::class);
        $this->refreshTokenService = new RefreshTokenService($this->em, 1000);
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
}