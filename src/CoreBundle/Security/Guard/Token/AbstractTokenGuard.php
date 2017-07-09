<?php

namespace CoreBundle\Security\Guard\Token;

use CoreBundle\Exception\ProgrammerException;
use CoreBundle\Factory\UserProviderFactory;
use CoreBundle\Security\Guard\GuardTrait;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * This provides the base for authenticating token based auths in our system.
 * Class AbstractTokenGuard
 * @package CoreBundle\Security\Guard\Token
 */
abstract class AbstractTokenGuard extends AbstractGuardAuthenticator
{
    use GuardTrait;

    /**
     * The place where the token is stored
     * @var string
     */
    const TOKEN_FIELD = 'token';

    /**
     * The field to store provider for authenticating
     * @var string
     */
    const TOKEN_TYPE_FIELD = 'type';

    /**
     * This means that security provider is facebook
     * @var string
     */
    const TOKEN_TYPE_FACEBOOK = 'facebook';

    /**
     * This means that security provider is google
     * @var string
     */
    const TOKEN_TYPE_GOOGLE = 'google';

    /**
     * This means we are authenticating the user via a token in the header, used mostly with the api
     * @var string
     */
    const TOKEN_TYPE_API = 'api';

    /**
     * This means that we are authenticating the user via refresh tokens
     * @var string
     */
    const TOKEN_TYPE_REFRESH = 'refresh_token';

    /**
     * @var UserProviderFactory
     */
    private $userProviderFactory;

    public function __construct(UserProviderFactory $userProviderFactory)
    {
        $this->userProviderFactory = $userProviderFactory;
    }

    /**
     * This gets a get the user provider for the third party authenticator and tries to fetch the user
     *
     * @param array $credentials
     * @param UserProviderInterface $userProvider
     * @return UserInterface
     */
    final public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $userProvider = $this->userProviderFactory->getUserProvider($credentials[self::TOKEN_TYPE_FIELD]);
        }
        catch (NotImplementedException $ex) {
            throw new UsernameNotFoundException('No invalid third party authentication network.', ProgrammerException::NO_TOKEN_PROVIDER_IMPLEMENTED);
        }

        return $userProvider->loadUserByUsername($credentials[self::TOKEN_FIELD]);
    }

    /**
     * This always returns true because if the user was found and token is valid
     * Internal tokens will be validated similar to third party tokens
     *
     * @param array $credentials
     * @param UserInterface $user
     * @return bool
     */
    final public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }
}