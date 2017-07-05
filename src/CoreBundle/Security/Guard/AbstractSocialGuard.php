<?php


namespace CoreBundle\Security\Guard;


use CoreBundle\Factory\SocialUserProviderFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Guard\GuardAuthenticatorInterface;

abstract class AbstractSocialGuard extends AbstractGuardAuthenticator
{
    const SOCIAL_TOKEN_FIELD = 'token';

    const SOCIAL_TOKEN_TYPE_FIELD = 'social_type';

    const TOKEN_TYPE_FACEBOOK = 'facebook';

    const TOKEN_TYPE_GOOGLE = 'google';


    /**
     * @var GuardAuthenticatorHandler
     */
    private $authenticatorHandler;
    /**
     * @var SocialUserProviderFactory
     */
    private $socialUserProviderFactory;

    public function __construct(GuardAuthenticatorHandler $authenticatorHandler, SocialUserProviderFactory $socialUserProviderFactory)
    {
        $this->authenticatorHandler = $authenticatorHandler;
        $this->socialUserProviderFactory = $socialUserProviderFactory;
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
        $userProvider = $this->socialUserProviderFactory->getUserProvider($credentials[self::SOCIAL_TOKEN_TYPE_FIELD]);

        if (empty($userProvider)) {
            throw new UsernameNotFoundException('No invalid third party authentication network.');
        }

        return $userProvider->loadUserByUsername($credentials[self::SOCIAL_TOKEN_FIELD]);
    }

    /**
     * This always returns true because if the user was found on the third party it means it's valid
     *
     * @param array $credentials
     * @param UserInterface $user
     * @return bool
     */
    final public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }


    /**
     * Called when authentication executed and was successful!
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the last page they visited.
     *
     * If you return null, the current request will continue, and the user
     * will be authenticated. This makes sense, for example, with an API.
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey The provider (i.e. firewall) key
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $this->authenticatorHandler->authenticateWithToken($token, $request);
        return new Response('Login Successful', Response::HTTP_OK);
    }

    /**
     * Called when authentication executed, but failed (e.g. wrong username password).
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the login page or a 403 response.
     *
     * If you return null, the request will continue, but the user will
     * not be authenticated. This is probably not what you want to do.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response('Authentication Failed.', Response::HTTP_FORBIDDEN);
    }

    /**
     * This happens when authentication is required but not provided.
     *
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return Response
     */
    final public function start(Request $request, AuthenticationException $authException = null)
    {
        return new Response('Authentication Required.', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Returns false becuase we don't support remember me
     * @return bool
     */
    final public function supportsRememberMe()
    {
        return false;
    }
}