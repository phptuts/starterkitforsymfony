---
layout: page
title: "Desktop Authentication (Facebook / Google)"
category: website
date: 2017-07-22 15:12:31
order: 2
disqus: 1
---

### Facebook Javascript Workflow

1) We load the facebook library asynchronously.
2) We attached a jquery on listener to open a facebook login modal
3) Once the user clicks on that btn a modal is fired.  Notice on the request for the modal we specify the scopes.

#### All of this is done in the [facebook-auth-js.html.twig](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Resources/views/main/common/social/facebook-auth-js.html.twig).

4) The access token is sent to the server and the session is authenticated. 
5) Redirect the user to their previous page if possible, otherwise send them home.

#### All this is done in the [token-auth-js.html.twig](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Resources/views/main/common/social/token-auth-js.html.twig).

### Google Javascript Workflow

One thing to note about google is that google required it's client key be put in the metadata of the page.

This is done here [layout.html.twig](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Resources/views/main/layout.html.twig#L7).

1) We attached a click event listener to the button.
2) When the button is click the user is taken to a new tab to authenticate.

All this happens in the [google-auth-js.html.twig](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Resources/views/main/common/social/google-auth-js.html.twig).

4) The access token is sent to the server and the session is authenticated. 
5) Redirect the user to their previous page if possible, otherwise send them home.

#### All this is done in the [token-auth-js.html.twig](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Resources/views/main/common/social/token-auth-js.html.twig).

## Backend

In our security.yml file we create a [token auth guard](https://phptuts.github.io/starterkitforsymfony/api/api-2-facebook-google-auth.html) for session.  This guard works exactly the same as the ApiLoginToken guard with 2 important differences.

```
main:
    pattern: ^/*
    anonymous: ~
    provider: app_proivder
    switch_user: true
    form_login:
        login_path: login
        check_path: login
    guard:
        authenticators:
            - AppBundle\Security\Guard\Token\SessionLoginTokenGuard

```

1) [onAuthenticateSuccess](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Security/Guard/Token/SessionLoginTokenGuard.php#L62): Just Authenticates the session
2) [getCredentials](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Security/Guard/Token/SessionLoginTokenGuard.php#L37): It looks for a different end point.

### Helpful Links

- [Symfony Authentication](https://symfony.com/doc/current/components/security/authentication.html)
- [Security Yaml Docs](https://symfony.com/doc/current/security.html)
- [Custom Guard in symfony](https://symfony.com/doc/current/security/guard_authentication.html)
- [Multiple Guards in symfony](https://symfony.com/doc/current/security/multiple_guard_authenticators.html)
- [User Providers](https://symfony.com/doc/current/security/multiple_user_providers.html)
- [Facebook Login](https://developers.facebook.com/docs/facebook-login/web/accesstokens)
- [Google Login](https://developers.google.com/identity/sign-in/web/sign-in)





