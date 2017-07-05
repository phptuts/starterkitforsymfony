<?php

namespace CoreBundle\Security\Guard;

use CoreBundle\Factory\SocialUserProviderFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class SessionSocialGuard extends AbstractSocialGuard
{

    /**
     * @var GuardAuthenticatorHandler
     */
    private $guardAuthenticatorHandler;

    public function __construct(SocialUserProviderFactory $socialUserProviderFactory, GuardAuthenticatorHandler $guardAuthenticatorHandler)
    {
        parent::__construct($socialUserProviderFactory);
        $this->guardAuthenticatorHandler = $guardAuthenticatorHandler;
    }

    /**
     * This authenticates the session and returns OK
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $this->guardAuthenticatorHandler->authenticateWithToken($token, $request);
        return new Response('Login Successful', Response::HTTP_OK);
    }
}