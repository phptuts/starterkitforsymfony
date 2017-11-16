<?php


namespace StarterKit\StartBundle\Model\Auth;

/**
 * Class AuthTokenModel
 * @package StarterKit\StartBundle\Model\User
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

    /**
     * Returns the array token & expiration timestamp
     *
     * @return array
     */
    public function getBody()
    {
        return [
            'token' => $this->getToken(),
            'expirationTimeStamp' => $this->getExpirationTimeStamp()
        ];
    }
}