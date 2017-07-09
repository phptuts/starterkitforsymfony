<?php

namespace CoreBundle\Model\User;

use CoreBundle\Entity\User;

/**
 * Class JWSModel
 * @package CoreBundle\Model\User
 */
class JWSUserModel
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
    private $token;

    /**
     * The timestamp the jws token expires
     * @var integer
     */
    private $expirationTimestamp;

    /**
     * JWSModel constructor.
     * @param User $user
     * @param string $token
     * @param integer $expirationTimestamp
     */
    public function __construct(User $user, $token, $expirationTimestamp)
    {
        $this->user = $user;
        $this->token = $token;
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
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return int
     */
    public function getExpirationTimestamp()
    {
        return $this->expirationTimestamp;
    }
}