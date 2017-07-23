---
layout: page
title: "Unit Tests"
category: test
date: 2017-07-22 15:12:31
order: 1
disqus: 1
---


### Mockery

I use mockery, a php library for mocking dependencies, to mock our dependencies.  I do this in the setup function and have class variables of all the dependencies.  A simple example of this can be found here:

[S3ServiceTest.php](https://github.com/phptuts/starterkitforsymfony/blob/master/tests/AppBundle/Service/S3ServiceTest.php)

We use phpunit 6, which now uses static Assert:: function to test everything.  It's not so bad once you get use to it.

I have all the service 100% unit tested.  We mock out everything and don't even touch the database with these tests.

### Helpful Links

- [Mockery Docs](http://docs.mockery.io/en/latest/)
- [PHPUnit](https://phpunit.de/manual/current/en/index.html)
- [Symfony Testing Best Practice](https://symfony.com/doc/current/best_practices/tests.html)
