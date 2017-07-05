<?php


namespace CoreBundle\Security\Guard;


use Symfony\Component\HttpFoundation\Request;

class GoogleGuard extends AbstractSocialGuard
{

    public function getCredentials(Request $request)
    {
        $post = json_decode($request->getContent(), true);

        if ($request->attributes->get('_route') == 'google_login_check' &&
            $request->isMethod(Request::METHOD_POST) &&
            !empty($post[self::SOCIAL_TOKEN_FIELD])
        ) {
            return [self::SOCIAL_TOKEN_TYPE_FIELD => self::TOKEN_TYPE_GOOGLE, self::SOCIAL_TOKEN_FIELD => $post[self::SOCIAL_TOKEN_FIELD]];
        }

        return null;

    }

}