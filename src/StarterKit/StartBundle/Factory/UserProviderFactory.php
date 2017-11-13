<?php

namespace StarterKit\StartBundle\Factory;

use StarterKit\StartBundle\Model\Credential\CredentialInterface;
use StarterKit\StartBundle\Security\Provider\EmailProviderInterface;
use StarterKit\StartBundle\Security\Provider\FacebookProviderInterface;
use StarterKit\StartBundle\Security\Provider\GoogleProviderInterface;
use StarterKit\StartBundle\Security\Provider\RefreshTokenProviderInterface;
use StarterKit\StartBundle\Security\Provider\TokenProviderInterface;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class SocialUserProviderFactory
 * @package StarterKit\StartBundle\Factory
 */
class UserProviderFactory implements UserProviderFactoryInterface
{

    /**
     * @var FacebookProviderInterface
     */
    private $facebookProvider;

    /**
     * @var GoogleProviderInterface
     */
    private $googleProvider;

    /**
     * @var RefreshTokenProviderInterface
     */
    private $refreshTokenProvider;

    /**
     * @var TokenProviderInterface
     */
    private $jwtTokenProvider;
    /**
     * @var EmailProviderInterface
     */
    private $emailProvider;

    public function __construct(
        FacebookProviderInterface $facebookProvider,
        GoogleProviderInterface $googleProvider,
        RefreshTokenProviderInterface $refreshTokenProvider,
        TokenProviderInterface $tokenProvider,
        EmailProviderInterface $emailProvider
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
    public function getClient($type)
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