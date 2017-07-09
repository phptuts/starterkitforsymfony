<?php


namespace CoreBundle\Model\Security;

/**
 * Class AuthTokenModel
 * @package CoreBundle\Model\User
 */
class AuthTokenModel
{
    /**
     * The Auth token
     * @var string
     */
    private $token;

    /**
     * The time stamp that the token will expire
     * @var integer
     */
    private $expirationTimeStamp;

    public function __construct($token, $expirationTimeStamp)
    {
        $this->token = $token;
        $this->expirationTimeStamp = $expirationTimeStamp;
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
    public function getExpirationTimeStamp()
    {
        return $this->expirationTimeStamp;
    }
}