<?php

namespace CoreBundle\Security\Guard;

use CoreBundle\Exception\ProgrammerException;
use CoreBundle\Factory\SocialUserProviderFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

abstract class AbstractSocialGuard extends AbstractGuardAuthenticator
{
    /**
     * The place where the token is stored
     * @var string
     */
    const SOCIAL_TOKEN_FIELD = 'token';

    /**
     * The field to store provider for authenticating
     * @var string
     */
    const SOCIAL_TOKEN_TYPE_FIELD = 'social_type';

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
     * @var SocialUserProviderFactory
     */
    private $socialUserProviderFactory;

    public function __construct(SocialUserProviderFactory $socialUserProviderFactory)
    {
        $this->socialUserProviderFactory = $socialUserProviderFactory;
    }

    /**
     * Gets the token and type
     *
     * @param Request $request
     *
     * @return array
     */
    final public function getCredentials(Request $request)
    {
        $post = json_decode($request->getContent(), true);

        if ($request->attributes->get('_route') == 'social_login_check' &&
            $request->isMethod(Request::METHOD_POST) &&
            !empty($post[self::SOCIAL_TOKEN_FIELD]) &&
            !empty($post[self::SOCIAL_TOKEN_TYPE_FIELD])
        ) {
            return $post;
        }

        return null;
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
            $userProvider = $this->socialUserProviderFactory->getUserProvider($credentials[self::SOCIAL_TOKEN_TYPE_FIELD]);
        }
        catch (NotImplementedException $ex) {
            throw new UsernameNotFoundException('No invalid third party authentication network.', ProgrammerException::NO_SOCIAL_PROVIDER_IMPLEMENTED);
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