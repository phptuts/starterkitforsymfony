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
    }
}

```

### Data is just a what response is

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
 
 
 