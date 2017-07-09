<?php

namespace CoreBundle\Model\User;

use CoreBundle\Entity\User;

/**
 * Class JWSModel
 * @package CoreBundle\Model\User
 */
class JWSModel
{
    /**
     * The user that the jws token belongs to
     * @var User
     */
    private $user;

    /**
     * The jws token
     * @var string
     */
    private $jwsToken;

    /**
     * The timestamp the jws token expires
     * @var integer
     */
    private $expirationTimestamp;

    /**
     * JWSModel constructor.
     * @param User $user
     * @param string $jwsToken
     * @param integer $expirationTimestamp
     */
    public function __construct(User $user, $jwsToken, $expirationTimestamp)
    {
        $this->user = $user;
        $this->jwsToken = $jwsToken;
        $this->expirationTimestamp = $expirationTimestamp;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getJwsToken()
    {
        return $this->jwsToken;
    }

    /**
     * @return int
     */
    public function getExpirationTimestamp()
    {
        return $this->expirationTimestamp;
    }
}