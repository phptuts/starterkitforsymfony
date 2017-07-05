<?php


namespace CoreBundle\Security\Guard;


use Symfony\Component\HttpFoundation\Request;

class FacebookGuard extends AbstractSocialGuard
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function getCredentials(Request $request)
    {
        $post = json_decode($request->getContent(), true);

        if ($request->attributes->get('_route') == 'facebook_login_check' &&
            $request->isMethod(Request::METHOD_POST) &&
            !empty($post[self::SOCIAL_TOKEN_FIELD])
        ) {
            return [self::SOCIAL_TOKEN_TYPE_FIELD => self::TOKEN_TYPE_FACEBOOK, self::SOCIAL_TOKEN_FIELD => $post[self::SOCIAL_TOKEN_FIELD]];
        }

        return null;
    }

}