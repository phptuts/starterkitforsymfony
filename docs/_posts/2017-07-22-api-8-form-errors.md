---
layout: page
title: "Form Errors"
category: api
date: 2017-07-22 15:12:31
order: 8
disqus: 1
---

### FOS Rest Default Form Error

By Default FOS RestBundle uses jms serializer or the symfony serializer to serialize form errors.  These do not conform to our response envelope so we over ride this by over riding a class parameter in the JMS serializer bundle that FOS is using to serialize the form.

[services.yml](https://github.com/phptuts/starterkitforsymfony/blob/master/app/config/services.yml)

```
jms_serializer.form_error_handler.class: AppBundle\Handler\FormErrorHandler
```

What you will notice if that FOS Rest Bundle over rides the JMS Serializer Bundle.  This class is almost copied from the FOS Rest Bundle implementation.  The only difference is how we are wrapping the response.
 
 [FormErrorHandler](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Handler/FormErrorHandler.php)
 
 ```
/**
  * This is what controls the response wrapper.
  *
  * @param \ArrayObject $serializedForm
  * @return array
  */
 protected function adaptFormArray(\ArrayObject $serializedForm)
 {
     return [
         'meta' => [
             'type' => 'formErrors',
             'paginated' => false,
         ],
         'data' => $serializedForm,
     ];
 }
 ```
 
 Example of json
 
 ```
 {
     "meta": {
         "type": "formErrors",
         "paginated": false
     },
     "data": {
         "children": {
             "email": {
                 "errors": [
                     "This value is already used."
                 ]
             },
             "plainPassword": {
                 "errors": [
                     "This value should not be blank."
                 ]
             }
         }
     }
 }
 ```
 
### Helpful Links
 - [FOS Form Errors](https://symfony.com/doc/master/bundles/FOSRestBundle/2-the-view-layer.html#forms-and-views)
 - [FOS Rest Bundle](https://symfony.com/doc/current/bundles/FOSRestBundle/index.html)
 