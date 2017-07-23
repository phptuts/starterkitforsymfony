---
layout: page
title: "Facebook / Google Auth"
category: api
date: 2017-07-22 15:12:31
order: 2
disqus: 1
---

## Using a facebook / google access token to authenticate vai the api.

You can authenticate a user via facebook or google by sending the api a access token and a type.  Right now the only 2 that are configured are facebook and google.  So the type of access token would be facebook or google. The api will will validate the token and if the token is valid it will log the user in.  If there is no user with an email address matching their facebook / google email the system will register the user and create an user with a random password.  For facebook to work you are required to ask in the scope for an email address.  If you don't do this it will fail.

Here more information on [facebook permission](https://developers.facebook.com/docs/facebook-login/permissions/).

```
{"token" : "facebook_access_token", "type": "facebook" }
```

```
{"token" : "google_access_token", "type": "google" }
```

