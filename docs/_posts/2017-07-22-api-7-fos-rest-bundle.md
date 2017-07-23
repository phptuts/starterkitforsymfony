---
layout: page
title: "FOS Rest Bundle Configuration"
category: api
date: 2017-07-22 15:12:31
order: 7
disqus: 1
---

### About [FOS Rest Bundle](https://symfony.com/doc/current/bundles/FOSRestBundle/index.html)

This bundle makes it easier to work with requests and responses when building an api.  We mainly use it with request because we use our [ResponseSerializerService](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Service/ResponseSerializerService.php) for serializing responses with the response envelope. One important thing to note is that we are using version 2 of the FOS Rest Bundle.

### Config

This is the first part of the config what this will do is disable all form csrf toknes from being required.  This is a great security practice for session websites but because there are no sessions when dealing with an api it makes sense to disable it.  It is disabled by user role, and all user have the IS_AUTHENTICATED_ANONYMOUSLY.

```
fos_rest:
    disable_csrf_role: IS_AUTHENTICATED_ANONYMOUSLY

```

What this will do is any controller method that has the  annotation will automatically put the json object into  the response.  So all you have to do now is $request->request->all() to get the json data.  Also all request will have to have a content-type of application/json.

```
    view:
        view_response_listener: 'force'
        formats:
            json: true
```

All this does is say that all our routes are going to json and remove the .json from the route.  

```
    routing_loader:
        default_format:  json
        include_format
```

This next part just controls which parts of the api the FOSRestBundle will intercept.  And that all this api will take in is json.

```
    format_listener:
        rules:
            - { path: '^/api', priorities: ['json'], fallback_format: json, prefer_extension: false }
            - { path: '^/*',  fallback_format: html } # Available for version >= 1.5

```

Here an example of using the FOS RestBundle in one of our controllers.  FOS RestBundle also allows us to define the url route as well.  It works very similar to symfony route annotations.

```
    /**
     * @Security("has_role('ROLE_USER')")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get's a user",
     *  section="Users",
     *  authentication=true
     * )
     *
     * @REST\View()
     * @REST\Get(path="users/{id}")
     *
     * @ParamConverter(name="user", class="AppBundle:User")
     *
     * @param User $user
     *
     * @return Response
     */
    public function getUserAction(User $user)
    {
        $this->denyAccessUnlessGranted(UserVoter::USER_CAN_VIEW_EDIT, $user);

        return $this->serializeSingleObject($user, [User::USER_PERSONAL_SERIALIZATION_GROUP], Response::HTTP_OK);
    }
```

### Helpful Links
- [FOS Rest Bundle](https://symfony.com/doc/current/bundles/FOSRestBundle/index.html)