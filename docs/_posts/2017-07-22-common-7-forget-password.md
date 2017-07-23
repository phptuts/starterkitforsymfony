---
layout: page
title: "Forget Password Workflow"
category: common
date: 2017-07-22 15:12:31
order: 7
disqus: 1
---


## WorkFlow

1) A request for a new password is asked for.  If the email address is not found a form error is returned.
2) If the email is found a forget password token is created with an expiration date.
3) Then we send an email out to the user giving them a link to reset their password.

This is all done in the [forget password service](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Service/User/ForgetPasswordService.php) and [ForgetPasswordForm](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Form/User/ForgetPasswordType.php).

Reset password will basically save check the token and make sure that the token is valid and has not expired.  If so it will let th user reset their password.

The workflow on for both api and website are the same.  Minus return types and pages.