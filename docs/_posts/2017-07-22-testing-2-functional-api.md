---
layout: page
title: "Functional Api Tests"
category: test
date: 2017-07-22 15:12:31
order: 2
disqus: 1
---


### WebTestCase

For the api tests I extend BaseApiTestCase which extends the WebTestCase in the LiipFunctionalTestBundle.  From there I create a client that can make a web request.  To simplify this I have created RequestTrait and BaseApiTestCase.  Right now the BaseApiTestCase has methods for confirming the credentialed response.

#### Side Note

Because all the services are private you will not be able to use the container.  In order to get around this I [manually assemble the JWSServic](https://github.com/phptuts/starterkitforsymfony/blob/master/tests/AppBundle/Controller/Api/BaseApiTestCase.php#L44) and use the [getRepository](https://github.com/phptuts/starterkitforsymfony/blob/master/tests/AppBundle/Controller/Api/BaseApiTestCase.php#L50) method in the entity manager to confirm stuff in the database.  The database that we use for sql lite database.  It's easier to setup with continuous integration because no password is required.

### Helpful Links

- [LiipFunctionalTestBundle](https://github.com/liip/LiipFunctionalTestBundle)
- [PHPUnit](https://phpunit.de/manual/current/en/index.html)
- [Symfony Testing Best Practice](https://symfony.com/doc/current/best_practices/tests.html)