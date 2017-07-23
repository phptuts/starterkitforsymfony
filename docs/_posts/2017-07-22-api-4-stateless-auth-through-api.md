---
layout: page
title: "Stateless Authentication with jwt / jws tokens"
category: api
date: 2017-07-22 15:12:31
order: 4
disqus: 1
---


## What is Stateless Authentication

All this really means is that every request will pass a token.  In our case it will be passed in the header because of some internet standard on some [rfc](https://tools.ietf.org/html/rfc6750).  I am not making this up.  Stateless just means that the api does not keep state between requests and will authenticate the user every time.  This is really great for mobile clients and other devices that don't have a concept of  a session.

## Guard and [security.yml](https://github.com/phptuts/starterkitforsymfony/blob/master/app/config/security.yml)
 
In order to implement stateless auth we use a custom guard.  A guard is a class that implements a [GuardAuthenticatorInterface](http://api.symfony.com/master/Symfony/Component/Security/Guard/GuardAuthenticatorInterface.html) that symfony uses to validate who a user is.   Guards are configured in the security.yml.  Here we are only using on guard.

```
api:
    pattern: ^/api
    anonymous: ~
    stateless: true
    guard:
        authenticators:
            - AppBundle\Security\Guard\Token\ApiTokenGuard

```

## Workflow

1) The request is passed to the getCredentials function in the ApiTokenGuard.  It checks the Authorization header in the request.  The header value is striped of the Bearer part so that only the token is return.  The type of token is also returned so that the AbstractTokenGuard can fetch the provider.

```
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

```
 2) Then the user is passed to in through getUser function in the [AbstractTokenGuard](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Security/Guard/Token/AbstractTokenGuard.php#L77).  This will fetch user provider for the type of token used.  If it can't find one an exception will be thrown. 
 
 
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
### [Token Provider](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Security/Provider/TokenProvider.php) Workflow

1. The jws is validated using the [JWS Service](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Service/Credential/JWSService.php).  If an invalid token is provided a UsernameNotFoundException is thrown.
2. The the payload of the token is decrypted and if the payload does not have a user_id key in it will throw a UsernameNotFoundException.  The payload of the token is a place where you can store custom set of data.  Think of it like a json object.
3. Then we look in our database for a user with that user id and if one is found we return the user.  Otherwise we throw a UsernameNotFoundException.


3) After that the checkCredentials function in the [AbstractTokenGuard](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Security/Guard/Token/AbstractTokenGuard.php#L98)  will be called we'll always return true.  This is because the user has already been validated. This is true for all token auths.

```
final public function checkCredentials($credentials, UserInterface $user)
{
    return true;
}
```


4) Then onAuthenticationSuccess is called and null is returned.  If null is returned in this function the request is passed through and authorization can then be checked.

``` 
public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
{
    return null;
}
```
### Helpful Links
- [Symfony Authentication](https://symfony.com/doc/current/components/security/authentication.html)
- [Security Yaml Docs](https://symfony.com/doc/current/security.html)
- [Custom Guard in symfony](https://symfony.com/doc/current/security/guard_authentication.html)
- [Multiple Guards in symfony](https://symfony.com/doc/current/security/multiple_guard_authenticators.html)
- [User Providers](https://symfony.com/doc/current/security/multiple_user_providers.html)


