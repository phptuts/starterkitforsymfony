---
layout: page
title: "Services"
category: common
date: 2017-07-22 15:12:31
order: 1
disqus: 1
---

### Symfony new Service

Symfony 3 now register all of your class minus repositories, and tests as services.  We excluded Models from the list as well.  They are all [autowired](https://symfony.com/doc/current/service_container/autowiring.html) by default meaning that no configuration is required.  Even controller our services.  The other thing to note is that all services our private.  What this means is that we have to use dependency injection in our controllers in order to get them the services they need.

Also all service are now registered under their full class name.  Meaning that the UserService's service name would be AppBundle\Service\UserService.  You can see a good example of this with the guards in the [security.yml](https://github.com/phptuts/starterkitforsymfony/blob/master/app/config/security.yml#L33).  Notice how we reference the full class name.

### Helpful Links

- [Symfony Best Practice For Business Logic](https://symfony.com/doc/current/best_practices/business-logic.html)