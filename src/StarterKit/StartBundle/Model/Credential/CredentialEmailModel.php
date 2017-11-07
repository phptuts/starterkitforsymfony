<?php

namespace StarterKit\StartBundle\Model\Credential;

/**
 * Class CredentialModelEmail
 * @package StarterKit\StartBundle\Model\Security
 */
class CredentialEmailModel implements CredentialInterface
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * CredentialModelEmail constructor.
     * @param string $email
     * @param string $password
     */
    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the Email as thing that will identify the user
     *
     * @return string
     */
    public function getUserIdentifier()
    {
        return $this->getEmail();
    }

    /**
     * Returns the type of user provider to user
     *
     * @return string
     */
    public function getProvider()
    {
        return CredentialInterface::PROVIDER_TYPE_EMAIL;
    }
}