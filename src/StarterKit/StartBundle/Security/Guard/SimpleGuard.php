<?php

namespace StarterKit\StartBundle\Security\Guard;

use StarterKit\StartBundle\Event\AuthFailedEvent;
use StarterKit\StartBundle\Event\UserEvent;
use StarterKit\StartBundle\Exception\ProgrammerException;
use StarterKit\StartBundle\Factory\UserProviderFactoryInterface;
use StarterKit\StartBundle\Model\Credential\CredentialInterface;
use StarterKit\StartBundle\Model\Credential\CredentialEmailModel;
use StarterKit\StartBundle\Model\Credential\CredentialTokenModel;
use StarterKit\StartBundle\Service\AuthResponseService;
use StarterKit\StartBundle\Service\AuthResponseServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Class Guard
 * @package StarterKit\StartBundle\Security\Guard
 */
class SimpleGuard extends AbstractGuardAuthenticator implements SimpleGuardInterface
{
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
     * The field where the email
     * @var string
     */
    const EMAIL_FIELD = 'email';

    /**
     * The field where the password
     * @var string
     */
    const PASSWORD_FIELD = 'password';

    /**
     * This is the key the token is stored under in the header
     * @var string
     */
    const AUTHORIZATION_HEADER = 'Authorization';

    /**
     * This is part of the rfc for sending token auth it's something prefixed to the token
     *
     * @var string
     */
    const BEARER = 'Bearer ';

    /**
     * This event is fired when authentication fails
     * @var string
     */
    const AUTH_FAILED_EVENT = 'auth_failed';

    /**
     * This happens when we return jwt tokens and refresh tokens.  AKA someone logs in a non stateless way.
     * This event is fired when a credentialed response is return
     * @var string
     */
    const AUTH_LOGIN_SUCCESS = 'auth_login_success';


    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var AuthResponseServiceInterface
     */
    private $authResponseService;

    /**
     * @var UserProviderFactoryInterface
     */
    private $userProviderFactory;
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * ApiLoginGuard constructor.
     * @param EncoderFactoryInterface $encoderFactory
     * @param AuthResponseServiceInterface $authResponseService
     * @param UserProviderFactoryInterface $userProviderFactory
     * @param  \Twig_Environment $twig
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        AuthResponseServiceInterface $authResponseService,
        UserProviderFactoryInterface $userProviderFactory,
        \Twig_Environment $twig,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->encoderFactory = $encoderFactory;
        $this->authResponseService = $authResponseService;
        $this->userProviderFactory = $userProviderFactory;
        $this->twig = $twig;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Step 1) See if the response has something to authenticate it and produce a CredentialInterface or return null
     *
     * @param Request $request
     * @return CredentialTokenModel|CredentialEmailModel
     */
    public function getCredentials(Request $request)
    {
        // We check that they are hitting the api login end point and that the request is a post
        if ($this->isLoginRequest($request)) {
            return $this->createCredentialModel($request);
        }

        // Otherwise we assume that it is regular request
        $jwtToken = $this->getJWTTokenFromRequest($request);

        if (!empty($jwtToken)) {
            return new CredentialTokenModel(CredentialInterface::PROVIDER_TYPE_JWT, $jwtToken);
        }

        return null;
    }

    /**
     * Step 2) Find the user based on the credential response.
     *
     * @param CredentialInterface $credentials
     * @param UserProviderInterface $userProvider
     * @return mixed
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            return $this
                ->userProviderFactory
                ->getClient($credentials->getProvider())
                ->loadUserByUsername($credentials->getUserIdentifier());
        } catch (NotImplementedException $ex) {
            throw new UsernameNotFoundException(
                'No invalid third party authentication network.',
                ProgrammerException::NO_TOKEN_PROVIDER_IMPLEMENTED
            );
        }
    }

    /**
     * Step 3) It it's a token response return true because the provider aka facebook will validate the token.
     *
     * Otherwise check the password against the user
     *
     * @param CredentialEmailModel|CredentialTokenModel $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        if ($credentials instanceof CredentialTokenModel) {
            return true;
        }

        $encoder = $this->encoderFactory->getEncoder($user);

        return $encoder->isPasswordValid($user->getPassword(), $credentials->getPassword(), $user->getSalt());
    }

    /**
     * Step 4a) Returns an Authentication Response if it is login request other returns null to allow the request to
     * continue
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return mixed
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($this->isLoginRequest($request)) {
            $user = $token->getUser();
            $this->dispatcher->dispatch(self::AUTH_LOGIN_SUCCESS, new UserEvent($user));
            return $this->authResponseService->createAuthResponse($user);
        }

        return null;
    }

    /**
     * Step 4b) This returns a 403 and happens when the authentication fails
     *
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $this->dispatcher->dispatch(self::AUTH_FAILED_EVENT, new AuthFailedEvent($request, $exception));
        if ($this->isRequestHtmlContentType($request)) {

            return new Response($this->twig->render('TwigBundle:Exception:error403.html.twig'), Response::HTTP_FORBIDDEN);
        }

        return new Response('Authentication Failed', Response::HTTP_FORBIDDEN);
    }

    /**
     * This returns a 401 and happens when auth is required but none is provided
     *
     * @param Request $request
     * @param AuthenticationException $authException
     *
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        if ($this->isRequestHtmlContentType($request)) {

            return new Response($this->twig->render('TwigBundle:Exception:error403.html.twig'), Response::HTTP_UNAUTHORIZED);
        }

        return new Response('Authentication Required', Response::HTTP_UNAUTHORIZED);

    }

    /**
     * We don't support remember me tokens
     *
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }

    /**
     * Returns true if the request is login
     *
     * @param Request $request
     * @return bool
     */
    private function isLoginRequest(Request $request)
    {
        return $request->getPathInfo() == '/login_check' && $request->isMethod(Request::METHOD_POST);
    }

    /**
     * @param Request $request
     * @return CredentialEmailModel|CredentialTokenModel|null
     */
    private function createCredentialModel(Request $request)
    {
        $post = $request->headers->get('Content-Type') === 'application/json' ?
            json_decode($request->getContent(), true) : $request->request->all();

        if ($this->isEmailLoginResponse($post)) {
            return new CredentialEmailModel($post[self::EMAIL_FIELD], $post[self::PASSWORD_FIELD]);
        }

        if ($this->isTokenLoginRequest($post)) {
            return new CredentialTokenModel($post[self::TOKEN_TYPE_FIELD], $post[self::TOKEN_FIELD]);
        }

        return null;
    }

    /**
     * Attempts to get the JWT token from the request,  Looks for the auth cookie and the header token
     *
     * @param Request $request
     * @return string|null
     */
    private function getJWTTokenFromRequest(Request $request)
    {
        $token = str_replace(self::BEARER, '', $request->headers->get(self::AUTHORIZATION_HEADER, ''));

        return !empty($token) ? $token : $request->cookies->get(AuthResponseService::AUTH_COOKIE);
    }

    /**
     * Return true if request has all fields for token login
     *
     * @param array $post
     * @return bool
     */
    private function isTokenLoginRequest($post)
    {
        return !empty($post[self::TOKEN_FIELD]) && !empty($post[self::TOKEN_TYPE_FIELD]);
    }

    /**
     * Returns true if request has all fields required for email password login
     *
     * @param array $post
     * @return bool
     */
    private function isEmailLoginResponse($post)
    {
        return !empty($post[self::EMAIL_FIELD]) && !empty($post[self::PASSWORD_FIELD]);
    }

    /**
     * Returns true if the request's content type is html
     *
     * @param Request $request
     * @return bool
     */
    private function isRequestHtmlContentType(Request $request)
    {
        return $request->headers->get('Content-Type') === 'html/text';
    }
}