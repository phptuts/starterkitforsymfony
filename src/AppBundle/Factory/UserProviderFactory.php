<?php


namespace AppBundle\Factory;

use AppBundle\Security\Guard\Token\AbstractTokenGuard;
use AppBundle\Security\Provider\FacebookProvider;
use AppBundle\Security\Provider\GoogleProvider;
use AppBundle\Security\Provider\RefreshTokenProvider;
use AppBundle\Security\Provider\TokenProvider;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class SocialUserProviderFactory
 * @package AppBundle\Factory
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
    private $tokenProvider;

    public function __construct(
        FacebookProvider $facebookProvider,
        GoogleProvider $googleProvider,
        RefreshTokenProvider $refreshTokenProvider,
        TokenProvider $tokenProvider
    ){

        $this->facebookProvider = $facebookProvider;
        $this->googleProvider = $googleProvider;
        $this->refreshTokenProvider = $refreshTokenProvider;
        $this->tokenProvider = $tokenProvider;
    }

    /**
     * Gets a third party auth provider based on type
     *
     * @param string $type
     * @return UserProviderInterface|null
     */
    public function getUserProvider($type)
    {
        if ($type == AbstractTokenGuard::TOKEN_TYPE_FACEBOOK) {
            return $this->facebookProvider;
        }

        if ($type == AbstractTokenGuard::TOKEN_TYPE_GOOGLE) {
            return $this->googleProvider;
        }

        if ($type == AbstractTokenGuard::TOKEN_TYPE_REFRESH) {
            return $this->refreshTokenProvider;
        }

        if ($type == AbstractTokenGuard::TOKEN_TYPE_API) {
            return $this->tokenProvider;
        }

        throw new NotImplementedException(sprintf("The '%s' social user provider has not been implemented.", $type));
    }
}