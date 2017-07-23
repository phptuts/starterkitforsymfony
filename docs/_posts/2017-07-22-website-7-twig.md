---
layout: page
title: "Twig"
category: website
date: 2017-07-22 15:12:31
order: 7
disqus: 1
---

## Twig Config

So there are few things to note

- We are using symfony bootstrap_3_form theme
- We have a custom field type for rendering file inputs.
- Under the globals we declared some global variables that can be used in any twig template.

```
twig:
    debug: '%kernel.debug%'
    strict_variables: '%kernel.debug%'
    form_themes:
        - 'bootstrap_3_layout.html.twig'
        - 'form/fields.html.twig'
    globals:
        facebook_app_id: '%app.facebook_app_id%'
        facebook_api_version: '%app.facebook_api_version%'
        email: '%app.from_email%'
        google_client_id: '%app.google_client_id%'

```

### FileUpload

If you want to use the file upload you will have to include some js.  See the [account setting page] for an example. 

### Helpful links

- [fields.html.twig](https://github.com/phptuts/starterkitforsymfony/blob/master/app/Resources/views/form/fields.html.twig)
- [How to customize a form field](https://symfony.com/doc/current/form/form_customization.html)
- [Form Theme](https://symfony.com/doc/current/form/form_customization.html)
- [BootStrap Form Theme](https://github.com/symfony/symfony/blob/master/src/Symfony/Bridge/Twig/Resources/views/Form/bootstrap_3_layout.html.twig)
- [Twig Global Variables](https://symfony.com/doc/current/templating/global_variables.html)