<?php

namespace StarterKit\StartBundle\Factory;

use StarterKit\StartBundle\Model\Credential\CredentialInterface;
use StarterKit\StartBundle\Security\Provider\EmailProvider;
use StarterKit\StartBundle\Security\Provider\FacebookProvider;
use StarterKit\StartBundle\Security\Provider\GoogleProvider;
use StarterKit\StartBundle\Security\Provider\RefreshTokenProvider;
use StarterKit\StartBundle\Security\Provider\TokenProvider;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class SocialUserProviderFactory
 * @package StarterKit\StartBundle\Factory
 */
class UserProviderFactory
{

    /**
     * @var FacebookProvider
     */
    private $facebookProvider;

    /**
     * @var GoogleProvider
     */
    private $googleProvider;

    /**
     * @var RefreshTokenProvider
     */
    private $refreshTokenProvider;

    /**
     * @var TokenProvider
     */
    private $jwtTokenProvider;
    /**
     * @var EmailProvider
     */
    private $emailProvider;

    public function __construct(
        FacebookProvider $facebookProvider,
        GoogleProvider $googleProvider,
        RefreshTokenProvider $refreshTokenProvider,
        TokenProvider $tokenProvider,
        EmailProvider $emailProvider
    ){

        $this->facebookProvider = $facebookProvider;
        $this->googleProvider = $googleProvider;
        $this->refreshTokenProvider = $refreshTokenProvider;
        $this->jwtTokenProvider = $tokenProvider;
        $this->emailProvider = $emailProvider;
    }

    /**
     * Gets a third party auth provider based on type
     *
     * @param string $type
     * @return UserProviderInterface|null
     */
    public function getUserProvider($type)
    {
        if ($type == CredentialInterface::PROVIDER_TYPE_FACEBOOK) {
            return $this->facebookProvider;
        }

        if ($type == CredentialInterface::PROVIDER_TYPE_GOOGLE) {
            return $this->googleProvider;
        }

        if ($type == CredentialInterface::PROVIDER_TYPE_REFRESH) {
            return $this->refreshTokenProvider;
        }

        if ($type == CredentialInterface::PROVIDER_TYPE_JWT) {
            return $this->jwtTokenProvider;
        }

        if ($type == CredentialInterface::PROVIDER_TYPE_EMAIL) {
            return $this->emailProvider;
        }


        throw new NotImplementedException(sprintf("The '%s' social user provider has not been implemented.", $type));
    }
}