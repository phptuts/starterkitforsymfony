---
layout: page
title: "Functional Testing Website"
category: test
date: 2017-07-22 15:12:31
order: 3
disqus: 1
---

## Forms

We use the liipfunctional test bundle to test form validation for pages.  Here an example in their [documentation](https://github.com/liip/LiipFunctionalTestBundle#basic-usage).
 
## The goal

We just want to make sure all the flows work.  So we test forget password workflow end to end.  We basically want to makes sure that all the happy paths work before a deploy.

## Side Notes

As discussed in the api section the database is a sql lite database.  Also all the services are private so to confirm stuff in the database we use the entity manager to fetch repositories.



### Helpful Links

- [LiipFunctionalTestBundle](https://github.com/liip/LiipFunctionalTestBundle)
- [PHPUnit](https://phpunit.de/manual/current/en/index.html)
- [Symfony Testing Best Practice](https://symfony.com/doc/current/best_practices/tests.html)