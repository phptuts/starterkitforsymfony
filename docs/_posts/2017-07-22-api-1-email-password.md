---
layout: page
title: "Api Email / Password Auth"
category: api
date: 2017-07-22 15:12:31
order: 1
disqus: 1
---


## Custom Guards

The api uses a custom guards to authenticate requests.  A guard is a class that implements the [GuardAuthenticatorInterface](http://api.symfony.com/master/Symfony/Component/Security/Guard/GuardAuthenticatorInterface.html).  It calls a series of methods and determines if the user is valid.  Guards only deal with authentication, meaning who the user is.  They don't deal with whether user has access or not.

What determines if symfony security will use a guard is in the [security.yml](https://github.com/phptuts/starterkitforsymfony/blob/master/app/config/security.yml) file.  In our you will notice we use multiple guards.  It will try the first one or the one specified in the entry point.  So it will try the ApiLoginGuard first.  This is the guard that authenticates the email and password.

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


### Workflow

1) Symfony will pass the request through the getCredentials function.  This will see if the json response has email and password fields.  If does not it will return null and authentication will fail.  When authentication fails here the start function will be called and a 401 will be returned.

[ApiLoginGuard](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Security/Guard/ApiLoginGuard.php#L75)
```
/**
 * This validates that the request is a login request and if so returns the email and password do it
 * Otherwise it will return null which will trigger the start method
 *
 * @param $request
 *
 * @return array
 */
public function getCredentials(Request $request)
{
    return $this->getLoginCredentials($request, [self::EMAIL_FIELD, self::PASSWORD_FIELD]);
}
```
[GuardTrait](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Security/Guard/GuardTrait.php#L38)
```
public function start(Request $request, AuthenticationException $authException = null)
{
    return new Response('Authentication Required.', Response::HTTP_UNAUTHORIZED);
}
```

2) Then it calls getUser and passes the email to the user provider.  The user provider for this for this api is configured in the security.yml file.  It uses the default user provider an will attempt to fetch the user by email.  If it does not find one it will throw an UsernameNotFoundException and authentication will fail.  The request will the pass through the guard trait on onAuthenticationFailure.  This will return a 403.

[security.yml](https://github.com/phptuts/starterkitforsymfony/blob/master/app/config/security.yml)

```
    providers:
        app_proivder:
            entity:
                class: AppBundle:User
                property: email

```
[GuardTrait](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Security/Guard/GuardTrait.php#L25)
```
public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
{
    return new Response('Authentication Failed.', Response::HTTP_FORBIDDEN);
}
```

3) Then the user password is authenticated, in the check credentials.  We get the password encoder factory and that uses the user to get password encoder.  That is configured in security.yml file as well.  We use bcrypt ot authenticate password.  If this function returns true the request is passed to onAuthenticationSuccess otherwise it is passed to onAuthenticationFailure in the guard trait and a 403 is returned.

```
/**
  * Returns true if the password matches the user found in the database
  *
  * @param array $credentials
  * @param UserInterface $user
  * @return bool
  */
 public function checkCredentials($credentials, UserInterface $user)
 {
     $encoder = $this->encoderFactory->getEncoder($user);
     return $encoder->isPasswordValid($user->getPassword(), $credentials[self::PASSWORD_FIELD], $user->getSalt());
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

[ApiLoginGuard](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Security/Guard/ApiLoginGuard.php#L115)

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
