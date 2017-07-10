<?php

namespace CoreBundle\Repository;

use CoreBundle\Entity\RefreshToken;
use CoreBundle\Entity\User;
use CoreBundle\Exception\ProgrammerException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class RefreshTokenRepository
 * @package CoreBundle\Repository
 */
class RefreshTokenRepository extends EntityRepository
{

    /**
     * Returns a user associated to a refresh token if one is found.
     * The token must not have been used and must have an expiration date in the future
     *
     * @param string $token
     * @return RefreshToken|null
     * @throws ProgrammerException
     */
    public function getValidRefreshToken($token)
    {
        try{
            $builder = $this->createQueryBuilder('r');

            return $builder->select('r')
                ->where($builder->expr()->gt('r.expires', ":now"))
                ->andWhere($builder->expr()->eq('r.used', ":used"))
                ->andWhere($builder->expr()->eq('r.token', ":token"))
                ->setParameters([
                    'token' => $token,
                    'used' => false,
                    'now' => new \DateTime()
                ])
                ->getQuery()
                ->getOneOrNullResult();

        }
        catch (NonUniqueResultException $ex) {
            throw new ProgrammerException('Duplicate Refresh Token Found in system.', ProgrammerException::REFRESH_TOKEN_DUPLICATE);
        }
    }
}