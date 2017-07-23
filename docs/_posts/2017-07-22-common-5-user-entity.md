---
layout: page
title: "User Entity"
category: common
date: 2017-07-22 15:12:31
order: 5
disqus: 1
---

The advanced user interface extends the user interface.  This is what is used to by all objects that can be used for authentication.  The important things to note:
### Important Methods:

- getUsername :return an email.  We only allow email login.  The user provider by default will call this method to get the field to look the user up by.
- isEnabled: If true the user can login otherwise the user can't
- eraseCredentials: is used to make sure the plain password is never serialized as part of the user.
- getSalt: Returns null because we are using bcrypt which does not require a salt.
