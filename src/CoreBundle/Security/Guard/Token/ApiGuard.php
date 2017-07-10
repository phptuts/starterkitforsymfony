<?php

namespace CoreBundle\Security\Guard\Token;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ApiGuard extends AbstractTokenGuard
{
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
     * This checks the header for an auth token if one exists we return the token and api
     *
     * @param Request $request
     * @return array|null
     */
    public function getCredentials(Request $request)
    {
        $token = str_replace(self::BEARER, '', $request->headers->get(self::AUTHORIZATION_HEADER, ''));

        if (empty($token)) {
            return null;
        }

        return ['token' => $token, 'type' => self::TOKEN_TYPE_API];
    }

    /**
     * We return null because we want the request to pass through to the controller
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

}