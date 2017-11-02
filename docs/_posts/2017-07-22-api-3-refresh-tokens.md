---
layout: page
title: "Refresh Token Auth"
category: api
date: 2017-07-22 15:12:31
order: 3
disqus: 1
---


## What is a refresh token?

A refresh token is a string that is used to reauthenticate the user without the user entering in their username or password.  The system stores refresh token with an expiration date and whether they have been used.  Refresh tokens in our system can only be used once.  The app.refresh_token_ttl determines how many seconds the refresh token will have before it expires.  

## [security.yml](https://github.com/phptuts/starterkitforsymfony/blob/master/app/config/security.yml) file

The refresh token are authenticated through a custom guard. A guard is a class that implements a [GuardAuthenticatorInterface](http://api.symfony.com/master/Symfony/Component/Security/Guard/GuardAuthenticatorInterface.html) that symfony uses to validate who a user is.   Guards are configured in the security.yml.  We use multiple guards in our configuration for one end point. It will try the ApiLoginGuard first because that is the specified in the entry_point.  This guard will return null when it tries to get information from the request and it will the try the ApiLoginTokenGuard.
 
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
### [Refresh Token UserProvider](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Security/Provider/RefreshTokenProvider.php) WorkFlow

We use the user service to try and find a user with a refresh token that has an expiration date in the future.  If we find one we extend the expiration date for the refresh token.  And return the user.  We have one refresh token for user, so that the phone can share the same refresh token and the web client.

```
$user = $this->userService->findUserByValidRefreshToken($username);

if (empty($user)) {
    throw new UsernameNotFoundException('No user found with refresh token provided.');
}

// This adds time to the refresh token.
$this->userService->updateUserRefreshToken($user);

return $user;
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
- [Refresh Token](https://auth0.com/learn/refresh-tokens/)

