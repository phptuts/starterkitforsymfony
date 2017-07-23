---
layout: page
title: "Api Email / Password Auth"
category: api
date: 2017-07-22 15:12:31
order: 1
disque: 1
---


## Using email and password to authenticate a user

The api uses a custom guards to authenticate requests.  A guard is a class that implements the [GuardAuthenticatorInterface](http://api.symfony.com/master/Symfony/Component/Security/Guard/GuardAuthenticatorInterface.html).  It calls a series of methods and determines if the user is valid.  Guards only deal with authentication, meaning who the user is.  They don't deal with whether user has access or not.

What determines if symfony security will use a guard is in the security.yml file.  In our you will notice we use multiple guards.  It will try the first one or the one specified in the entry point.  So it will try the ApiLoginGuard first.  This is the guard that authenticates the email and password.

### Steps to authenticate

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

