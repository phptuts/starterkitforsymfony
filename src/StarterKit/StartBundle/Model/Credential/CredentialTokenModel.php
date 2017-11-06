<?php

namespace StarterKit\StartBundle\Model\Credential;

/**
 * Class CredentialTokenModel
 * @package StarterKit\StartBundle\Model\Security
 */
class CredentialTokenModel implements CredentialInterface
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $provider;

    public function __construct($provider, $token)
    {
        $this->token = $token;
        $this->provider = $provider;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Returns the token as the thing that will identify the user
     *
     * @return string
     */
    public function getUserIdentifier()
    {
        return $this->getToken();
    }

    /**
     * Returns the type of user provider to user
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }


}