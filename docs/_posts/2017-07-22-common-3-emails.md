---
layout: page
title: "Emails"
category: common
date: 2017-07-22 15:12:31
order: 3
disqus: 1
---

Emails are done via the swiftmailer.  We use twig to construct the email body and I personally use smtp service to send out the email.  But you are free to build your own mailing server.  The one parameter you will need to configure is the [app.from_email](https://github.com/phptuts/starterkitforsymfony/blob/master/app/config/parameters.yml.dist) parameter. This controls where are the emails are being send from.

The service that handles all the emails is the [EmailService](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Service/EmailService.php).

Also note that by default the test environment does not send out emails.

[config_test.yml](https://github.com/phptuts/starterkitforsymfony/blob/master/app/config/config_test.yml#L16)

### Helpful Links
- [SwiftMailer](http://symfony.com/doc/current/email.html)