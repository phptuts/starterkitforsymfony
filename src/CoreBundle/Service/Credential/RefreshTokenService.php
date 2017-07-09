<?php

namespace CoreBundle\Service\Credential;


use CoreBundle\Entity\RefreshToken;
use CoreBundle\Entity\User;
use CoreBundle\Service\AbstractEntityService;
use Doctrine\ORM\EntityManager;

class RefreshTokenService extends AbstractEntityService
{
    /**
     * @var int
     */
    private $refreshTokenTTL;

    public function __construct(EntityManager $em, $refreshTokenTTL)
    {
        parent::__construct($em);
        $this->refreshTokenTTL = $refreshTokenTTL;
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
                ->setToken(bin2hex(random_bytes(90)))
            ->setUser($user);

        $expirationDate = new \DateTime();
        $expirationDate->modify('+' . $this->refreshTokenTTL . ' seconds');

        $refreshToken->setExpires($expirationDate);

        $this->save($refreshToken);

        return $refreshToken;
    }
}