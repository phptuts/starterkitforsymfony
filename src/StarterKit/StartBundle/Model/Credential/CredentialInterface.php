<?php

namespace StarterKit\StartBundle\Model\Credential;

/**
 * Class CredentialModel
 * @package StarterKit\StartBundle\Model\Cre
 */
interface CredentialInterface
{

    /**
     * This means that provider is using email and password
     * @var string
     */
    const PROVIDER_TYPE_EMAIL = 'email';

    /**
     * This means that security provider is facebook
     * @var string
     */
    const PROVIDER_TYPE_FACEBOOK = 'facebook';

    /**
     * This means that security provider is google
     * @var string
     */
    const PROVIDER_TYPE_GOOGLE = 'google';

    /**
     * This means we are authenticating the user via a token in the header, used mostly with the api
     * @var string
     */
    const PROVIDER_TYPE_JWT = 'jwt';

    /**
     * This means that we are authenticating the user via refresh tokens
     * @var string
     */
    const PROVIDER_TYPE_REFRESH = 'refresh_token';


    /**
     * Returns the type of user provider to user
     *
     * @return string
     */
    public function getProvider();

    /**
     * Returns the string to identify the user
     *
     * @return string
     */
    public function getUserIdentifier();
}