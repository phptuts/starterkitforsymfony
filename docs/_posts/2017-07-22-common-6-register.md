---
layout: page
title: "Registration Service"
category: common
date: 2017-07-22 15:12:31
order: 6
disqus: 1
---

Registration is almost the same for the api as it is for the website.  They both login the user as soon as the user is registered.  Api will return a [credentialed response](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Controller/Api/RegisterController.php#L69) and website will [authenticate the session](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Controller/Main/RegisterController.php#L49).

### [Register Service](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Service/User/RegisterService.php)

The register service does 2 things:

- Saves the user with a plain password
- Sends the user a welcome email

Everything time a user is created in system it goes through the register service.

