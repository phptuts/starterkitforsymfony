---
layout: page
title: "Facebook / Google Auth"
category: api
date: 2017-07-22 15:12:31
order: 2
disqus: 1
---

## Access Tokens

You can authenticate a user via facebook or google by sending the api a access token and a type.  Right now the only 2 that are configured are facebook and google.  So the type of access token would be facebook or google. The api will will validate the token and if the token is valid it will log the user in.  If there is no user with an email address matching their facebook / google email the system will register the user and create an user with a random password.  For facebook to work you are required to ask in the scope for an email address.  If you don't do this it will fail.

Here more information on [facebook permission](https://developers.facebook.com/docs/facebook-login/permissions/).

```
{"token" : "facebook_access_token", "type": "facebook" }
```

```
{"token" : "google_access_token", "type": "google" }
```

## Custom Guards

In order to authenticate via facebook we use a custom guard.  A guard is a class that implements a [GuardAuthenticatorInterface](http://api.symfony.com/master/Symfony/Component/Security/Guard/GuardAuthenticatorInterface.html) that symfony uses to validate who a user is.  Guards are configured in the security.yml.  We use multiple guards in our configuration for one end point. It will try the ApiLoginGuard first because that is the specified in the entry_point.  This guard will return null when it tries to get information from the request and it will the try the ApiLoginTokenGuard.

[security.yml](https://github.com/phptuts/starterkitforsymfony/blob/master/app/config/security.yml)
```
api_login:
    pattern: ^/api/login_check
    stateless: true
    provider: app_proivder
    guard:
        authenticators:
            - AppBundle\Security\Guard\ApiLoginGuard
            - AppBundle\Security\Guard\Token\ApiLoginTokenGuard
        entry_point: AppBundle\Security\Guard\ApiLoginGuard

```

## Workflow

1) The guard will call the getCredentials function and see if the request has a token and a type of token in the json being sent.  If it does not it will return null and the start function will call the start function and return a 401.

[ApiLoginTokenGuard](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Security/Guard/Token/ApiLoginTokenGuard.php#L47)
```
/**
 * Gets the token and type for the website users
 *
 * @param Request $request
 *
 * @return array
 */
public function getCredentials(Request $request)
{
    return $this->getLoginCredentials($request,[self::TOKEN_FIELD, self::TOKEN_TYPE_FIELD]);
}
```
[GuardTrait](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Security/Guard/GuardTrait.php#L38)
```
public function start(Request $request, AuthenticationException $authException = null)
{
    return new Response('Authentication Required.', Response::HTTP_UNAUTHORIZED);
}
```

2) Then the user is passed to in through getUser function in the [AbstractTokenGuard](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Security/Guard/Token/AbstractTokenGuard.php#L77).  This will fetch user provider for the type of token used.  If it can't find one an exception will be thrown.  From here it will go to our custom user provider.  In this doc we will review facebook but it's same process for google.


```
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
```

### [Facebook User Provider](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Security/Provider/FacebookProvider.php) Workflow

 1. The first thing we will do is look for a user in our database that has facebook user id that matches the person trying to log in.  If we find one we'll return that user.  The reason we look for the facebook user_id first that may be using different email or changed their email on facebook and we don't want to create duplicate users.
 
 2. We then check and see if an email address matches the one they are trying to login with.  If it does we save the facebook user id with that user and return the user object.
 
 3. If we can't find a user at all we'll save a user with facebook user id using the register service.  This service will send out a registration email.

 If the token is not valid this will throw a username not found exception. One interesting thing to note is that provider is validating the credentials and not the checkCredentials function.
 
```
    /**
     * 1) We validate the access token by fetching the user information
     * 2) We search for facebook user id and if one is found we return that user
     * 3) Then we search by email and if one is found we update the user to have that facebook user id
     * 4) If nothing is found we register the user with facebook user id
     *
     * @param string $username The facebook auth token used to fetch the user
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        try {
            // If you want to request the user's picture you can picture
            // You can also specify the picture height using picture.height(500).width(500)
            // Be sure request the scope param in the js
            $response = $this->facebookClient->get('/me?fields=email', $username);
            $facebookUser = $response->getGraphUser();
            $email = $facebookUser->getEmail();
            $facebookUserId = $facebookUser->getId();
            $user = $this->userService->findByFacebookUserId($facebookUserId);
            // We always check their facebook user id first because they could have change their email address
            if (!empty($user)) {
                return $user;
            }
            $user = $this->userService->findUserByEmail($email);
            // This means that user already register and we need to associate their facebook account to their user entity
            if (!empty($user)) {
                $this->updateUserWithFacebookId($user, $facebookUserId);
                return $user;
            }
            
            // This means no user was found and we need to register user with their facebook user id
            return $this->registerUser($email, $facebookUserId);
        } catch (FacebookResponseException $ex) {
            throw new UsernameNotFoundException("Facebook AuthToken Did Not validate, ERROR MESSAGE " . $ex->getMessage(), ProgrammerException::FACEBOOK_RESPONSE_EXCEPTION_CODE);
        } catch (FacebookSDKException $ex) {
            throw new UsernameNotFoundException("Facebook SDK failed, ERROR MESSAGE " . $ex->getMessage(), ProgrammerException::FACEBOOK_SDK_EXCEPTION_CODE);
        } catch (\Exception $ex) {
            throw new UsernameNotFoundException("Something unknown went wrong, ERROR MESSAGE  " . $ex->getMessage(), ProgrammerException::FACEBOOK_PROVIDER_EXCEPTION);
        }
    }
```

3) After that the checkCredentials function in the [AbstractTokenGuard](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Security/Guard/Token/AbstractTokenGuard.php#L98)  will be called we'll always return true.  This is because the user has already been validated. This is true for all token auths.

```
final public function checkCredentials($credentials, UserInterface $user)
{
    return true;
}
```


4) The request is then passed to onAuthenticationSuccess and a credentialed response is returned.  This will have a jws token for stateless authentication.  A refresh token for getting a new jws token without email password login.  As well a serialized user with serialization group user.  



```
 {
        "meta": {
            "type": "credentials",
            "paginated": false
        },
        "data": {
            "user": {
                "id": "96430bcc-6987-11e7-9d99-08002732ed09",
                "displayName": "update_user_e2e",
                "email": "update_user_e2e@email.com",
                "bio": null
            },
            "tokenModel": {
                "token": "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9.eyJ1c2VyX2lkIjoiOTY0MzBiY2MtNjk4Ny0xMWU3LTlkOTktMDgwMDI3MzJlZDA5IiwiZXhwIjoxNTA1NDE1ODcxLCJpYXQiOjE1MDAyMzE4NzF9.vKuQmpOFPneh38vFnT7BJPqT89gaIq8MEcL4SrDUHvQ8Jpq0z-JVEex8vbSKJFORFwPGnw2X4xWgx-qs39C0T06oknn2fHF-jLOafwjwCRLTDeOyrDT6JX2sNxEirfS1kzvXL_lA74JuZO8g1twmjHiFSlvk2j32ueo9VnZZdisHvYHnl2zy8mgme3A8izKQsgw2UHBsSPy6x4fe80dWnf60Wp5NPZkBRtAPitE4SLktnJEVo93aSzUPVQiDfKPdA4J0zE7UfsmkDIqMflOIZI_CSCuKGJ77q8WWcziH47P_Qv4hF93s19hI9PAb1mMv75LrVc82JrftHyRC_wk_LF1J6al7lcKNWv9paw0VLJVHz-qBRY-LOFwkUzQNMZetXab_VA_FPTeR0itHZDku5Et64clb9_TzFeveQ7Q0W2yakPsFCDK24a1SxTqzVXMKSAiecQK6oFsSTSsDEekKlkrpXshHN3LlQ_OnDAyp-J8Bzl90MAE2VlP-WFEpNnFzH3G6apTkQ31RYNaV6EFC-TOv_rMmKvM9O0E7NezSPEs15jGSVEzd_I5Q44GkEij1mPij-F1pqjvVbbD81_MZZIon8QsS9hTWjCqHUxzAvoSZ_y7nheYGzwzxWc_dz2qN8v1ragbQrLAaUST12TLIAVE22Q_JPhHmI0wQi0u95Kk",
                "expirationTimeStamp": 1505415871
            },
            "refreshTokenModel": {
                "token": "6fd9225321cc4867ff9c7f77cd748f23ce9a5186e6dbbae4f4a720aab7a7879bb9af60669e1fca45bf0d9a3033ff6f9a07a06c50996fa8406dcff2ecd2ba0955f994aa24d3b667dcf28e24f4d23fda666cf8d7a155ddef701796",
                "expirationTimeStamp": 1510599871
            }
        }
    }

```

[ApiLoginTokenGuard](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Security/Guard/Token/ApiLoginTokenGuard.php#L60)

```
public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
{
    return $this->credentialResponseBuilderService->createCredentialResponse($token->getUser());
}
```
### Helpful Links

- [Symfony Authentication](https://symfony.com/doc/current/components/security/authentication.html)
- [Security Yaml Docs](https://symfony.com/doc/current/security.html)
- [Custom Guard in symfony](https://symfony.com/doc/current/security/guard_authentication.html)
- [Multiple Guards in symfony](https://symfony.com/doc/current/security/multiple_guard_authenticators.html)
- [User Providers](https://symfony.com/doc/current/security/multiple_user_providers.html)
- [Facebook Login](https://developers.facebook.com/docs/facebook-login/web/accesstokens)
- [Google Login](https://developers.google.com/identity/sign-in/web/sign-in)
