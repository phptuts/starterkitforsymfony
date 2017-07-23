---
layout: page
title: "Admin / User Management"
category: website
date: 2017-07-22 15:12:31
order: 5
disqus: 1
---

### User Search

User search is not by sending 2 optional query params.

- q: this is the one where the search term goes
- page: this is the page of is on.  There is pagination at the bottom of the page.

## Contents

[index.html.twig](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Resources/views/admin/users/index.html.twig)

What is generated is a table using a twig macro function.  List all the users in the page.  

We also generate pagination as well.

## Functionality

- You can disable / enable a user to login
- You can change user's email
- You can autogenerate a user's password
- Login as that user using the _switch_user query param

## Javascript Libraries Used

- [bootstrap-toggle](http://www.bootstraptoggle.com/)
- [bootstrap-notify](http://bootstrap-notify.remabledesigns.com/)

Let me know if you guys want more documentation and what you want documented here.
