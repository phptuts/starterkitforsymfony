---
layout: page
title: "Email Password Login"
category: website
date: 2017-07-22 15:12:31
order: 1
disqus: 1
---


Email and password login is done through the form_login in symfony security.yml file. Notice that we use the user_provider located under the provider section.  Also note that it looks for email as the property and not username.  

#### Form Login With Provider

```
    provider: app_proivder
    switch_user: true
    form_login:
        login_path: login
        check_path: login

```

#### User Provider 

```
    providers:
        app_proivder:
            entity:
                class: AppBundle:User
                property: email

```


There is nothing really to document here because symfony already done it.  Read [Symfony Form Login](https://symfony.com/doc/current/security.html#b-configuring-how-users-are-loaded).

### HelpFul Links

- [Form Login](https://symfony.com/doc/current/security/form_login_setup.html)



