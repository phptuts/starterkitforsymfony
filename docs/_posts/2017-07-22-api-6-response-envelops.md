---
layout: page
title: "Response Envelopes & Serialization"
category: api
date: 2017-07-22 15:12:31
order: 6
disqus: 1
---


## Why a response envelope

The reason you want to wrap everything in similar response envelope is so that clients can easily figure out what type of response it is and pull in the right parser to get the data.  This will make your mobile devs very happy.  Trust me.

## The parts of our response envelope

### Meta

This is the part where we store the type, page information for list responses, and where or not it is a paginated response.  If it is a paginated response we return the current page, number of pages, and number items per page.

#### Paginated Response
```
{
    "meta": {
        "type": "users",
        "paginated": true,
        "total": 117,
        "page": 1,
        "pageSize": 10,
        "totalPages": 12
    },
    "data": [
        ...
    ]
}
```

#### Non Paginated Response
```
{
    "meta": {
        "type": "users",
        "paginated": false,
    },
    "data" : {
        ...
    }
```
The data part of the response is just what is being serialized.  In most of our example that would the user.

## Workflow

1) Create a ResponseModel.  We have to a [ResponseModel](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Model/Response/ResponseModel.php) for single items and a [ResponsePageModel](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Model/Response/ResponsePageModel.php) for a list of items.  Each of these implement a ResponseInterface which has a getBody Method.  This is where you create the response envelope.  

#### Response Model Example

```
/**
 * Create a response envelope that wraps the data.
 *
 * @return array
 */
public function getBody()
{
    return [
        'meta' => [
            'type' => $this->data->getResponseType(),
            'paginated' => false
        ],
        'data' => $this->data
    ];
}
```

2) The response model is then passed into the [ResponseSerializeService](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Service/ResponseSerializerService.php).  This will take a [ResponseModelInterface](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Model/Response/ResponseModelInterface.php) interface and serialize it with the JMSSerializer Bundle.  Serialization is done in the standard way through entity annotations.  See the [User](https://github.com/phptuts/starterkitforsymfony/blob/master/src/AppBundle/Entity/User.php) entity for an example.
 
 ## JMS Serializer Notes
 
 We use [JMS\Serializer\Naming\IdenticalPropertyNamingStrategy](https://knpuniversity.com/screencast/symfony-rest/serializer-basics) for serializing our json.  This means that the json will be camelcased and not snake cased.  This is being done in the [services.yml](https://github.com/phptuts/starterkitforsymfony/blob/master/app/config/services.yml#L5) file.  The property we are over riding is,  jms_serializer.serialized_name_annotation_strategy.class.
 
 We use annotations with the jms serializer.  Also for the user we use [Exclusion Policy=All](http://jmsyst.com/libs/serializer/master/reference/annotations#exclusionpolicy).  What this means is that we have to use the [Expose annotation](http://jmsyst.com/libs/serializer/master/reference/annotations#expose) to make it visible to the client consuming the api.  We also use [serialization groups](http://jmsyst.com/libs/serializer/master/reference/annotations#groups) to hide and expose properties of the user.  A google example is if the user or admin is accessing a user you may want to expose the user's email address, but if user is just being serialized as part of a a piece of content, say she is author, then you don't want to expose their email address.
 
 ### Helpful Links

 - [JMS Serialization Library Docs](http://jmsyst.com/libs/serializer)
 - [JMS Serializer Bundle Doc](http://jmsyst.com/bundles/JMSSerializerBundle)