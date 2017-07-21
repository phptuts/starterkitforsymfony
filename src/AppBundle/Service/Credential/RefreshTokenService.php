<?php

namespace AppBundle\Service\Credential;


use AppBundle\Entity\RefreshToken;
use AppBundle\Entity\User;
use AppBundle\Repository\RefreshTokenRepository;
use AppBundle\Service\AbstractEntityService;
use Doctrine\ORM\EntityManager;

/**
 * Class RefreshTokenService
 * @package AppBundle\Service\Credential
 */
class RefreshTokenService extends AbstractEntityService
{
    /**
     * @var int
     */
    private $refreshTokenTTL;

    /**
     * @var RefreshTokenRepository
     */
    private $refreshTokenRepository;

    /**
     * RefreshTokenService constructor.
     * @param EntityManager $em
     * @param $refreshTokenTTL
     * @param RefreshTokenRepository $refreshTokenRepository
     */
    public function __construct(EntityManager $em, RefreshTokenRepository $refreshTokenRepository, $refreshTokenTTL)
    {
        parent::__construct($em);
        $this->refreshTokenTTL = $refreshTokenTTL;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    /**
     * Create and saves a refresh token for the user
     *
     * @param User $user
     * @return RefreshToken
     */
    public function createRefreshToken(User $user)
    {
        $refreshToken = new RefreshToken();
        $refreshToken->setUser($user)
                ->setToken(bin2hex(random_bytes(90)));

        $expirationDate = new \DateTime();
        $expirationDate->modify('+' . $this->refreshTokenTTL . ' seconds');

        $refreshToken->setExpires($expirationDate);

        $this->save($refreshToken);

        return $refreshToken;
    }

    /**
     * Returns null or a valid refresh token
     *
     * @param $token
     * @return RefreshToken|null
     */
    public function getValidRefreshToken($token)
    {
        return $this->refreshTokenRepository->getValidRefreshToken($token);
    }
}