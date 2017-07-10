The most important factor when designing an API response is having a standardised format of the response. 
Most enterprises have defined their own custom API format, usually a JSON response that maps neatly to their own data model. 
For Joomla, we will look at the most useful and most commonly used response formats which are created by the open source community.

All JSON response standards below supports [HATEOAS](https://en.wikipedia.org/wiki/HATEOAS) approach.

##  [JSON API](http://jsonapi.org/format/)

JSON:api does what the REST community has mostly avoided, use the HTTP methods as they should be used.
JSON API is also used by Drupal and has a widely used plugin for Wordpress.

Example HTTP 200 Response
```javascript

HTTP/1.1 200 OK
Content-Type: application/vnd.api+json

{
  "data": [{
    "type": "articles",
    "id": "1",
    "attributes": {
      "title": "JSON API paints my bikeshed!",
      "body": "The shortest article. Ever.",
      "created": "2015-05-22T14:56:29.000Z",
      "updated": "2015-05-22T14:56:28.000Z"
    },
    "relationships": {
      "author": {
        "data": {"id": "42", "type": "people"}
      }
    }
  }],
  "included": [
    {
      "type": "people",
      "id": "42",
      "attributes": {
        "name": "John",
        "age": 80,
        "gender": "male"
      }
    }
  ]
}

```

### Plus Side
* Does not use child documents and instead flattens the entire graph of objects at the top level.
* Attributes and relationships of a given resource are separate. (Relationships can be excluded if not needed by the receiver/client).
* Advised for large-scale public APIs.
* (Relatively) easily understandable specification.

### Minus Side
* Doesn’t offer smooth migration for already-built APIs.

### Error Handling
* Detailed: Has a specified format for errors.

Example Error Response
```javascript

HTTP/1.1 422 Unprocessable Entity
Content-Type: application/vnd.api+json

{
  "errors": [
    {
      "status": "422",
      "source": { "pointer": "/data/attributes/first-name" },
      "title":  "Invalid Attribute",
      "detail": "First name must contain at least three characters."
    }
  ]
}

```

## [JSON-LD](https://en.wikipedia.org/wiki/JSON-LD)

JSON for linked documents is the first JSON standardised response structure (format) that we are going to look at. 
JSON-LD introduces @context keyword which defines set of terms within the response. 
Within the context properties are assigned to a URL that provides documentation about the meaning of that property.

Example HTTP 200 Response
```javascript

{
  "@context": "http://json-ld.org/contexts/person.jsonld",
  "@id": "http://dbpedia.org/resource/John_Lennon",
  "name": "John Lennon",
  "born": "1940-10-09",
  "spouse": "http://dbpedia.org/resource/Cynthia_Lennon"
}

```

### Plus Side
* JSON-LD offers a smooth migration for already-built APIs.
* It is a W3C recommendation (https://www.w3.org/blog/news/archives/3589). 

### Minus Side
* RDF (Resource Description Framework) structures that go beyond key/value pairs (i.e. property/object pairs attached to a given subject) are not really human readable.
* Relatively complex specification.

### Error Handling
* Advanced: Uses error types.


## [JSend](https://labs.omniti.com/labs/jsend)

JSend is a specification that lays down some rules for how JSON responses from web servers should be formatted. 
JSend focuses on application-level (as opposed to protocol- or transport-level) messaging which makes it ideal for use in REST-style applications and APIs.

Example HTTP 200 Response
```javascript

{
    status : "success",
    data : {
        "posts" : [
            { "id" : 1, "title" : "A blog post", "body" : "Some useful content" },
            { "id" : 2, "title" : "Another blog post", "body" : "More content" },
        ]
     }
}

```

### Plus Side
* Easy to understand and implement.
* Simple design.

### Minus Side
* Poor specification.
* Not capable of handling complex responses in a neat way.

### Error Handling
* Basic/Weak

Example Error Response
```javascript

{
    "status" : "error",
    "message" : "Unable to communicate with database"
}

```

## Also Worth Considering

### [Hypertext Application Language or HAL in short](http://stateless.co/hal_specification.html)
Hypertext Application Language (HAL) is a simple format that gives a consistent and easy way to hyperlink between resources in your API. 
It is referred as an Internet Draft a “work in progress” standard convention for defining hypermedia such as links to external resources within JSON or XML code. 

## Conclusion
Since the aim is having an API that will serve to many developers from various backgrounds, and also considering the wide variety of data type and amount the API will need to handle, I believe it the best option would be using [JSON API](http://jsonapi.org/format/) for Joomla Webservices API.
