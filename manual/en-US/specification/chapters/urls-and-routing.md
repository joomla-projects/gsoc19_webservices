## Rest API
At this moment in time Joomla is only interested in providing a REST API for it's webservices. We do not expect to implement
a SOAP API (or other non-REST webservices in the near future.)

### URL Structure
URL Route: The REST API will always start with /api.

#### Component Example
The following are examples for the Joomla content component (com_content)
  - List Articles (GET /api/v1/articles)
  - Retrieve a Article (GET /api/v1/articles/999)
  - Create a Article (POST /api/v1/articles)
  - Update a Article (PUT /api/v1/articles/999)
  - Delete a Article (DELETE /api/v1/articles/999)

> Api/v1/ should be defined by the extension routes (v1 is not static). Versioning is probably going to be something defined at a component level.

### Responses
We expect that responses to the framework will go through at least two phases:

#### Phase 1
Entity access level - Serialization from a model's getItem() - with limited formatting (e.g. custom fields should be
moved to be top level items etc.). This will only support JSON responses.

Note however that we do still expect responses to conform to the Richardson Maturity Model Level 3

**TODO: How should the routes from extensions be aware of each other for providing related links (related to
[Extensibility](specification/chapters/extensibility.md))**

#### Phase 2
Phase 2 will support content and language negotiations to format the responses using something similar to [this php
library](https://github.com/willdurand/Negotiation).

#### Phase 3
Move beyond simple serialization towards:
  - Full serialization (E.g. Sylius or FriendsOfSymfony/FOSRestBundle)
  - Column by column

### The Joomla Framework Router
The most important framework package we will start to use as part of the webservice project is the Framework routing package (version 2)

This router is designed to map a URL to a controller. You can find sample code to define routes in Joomla! Framework at: [https://github.com/joomla/framework.joomla.org/blob/master/src/Service/ApplicationProvider.php#L216-L263](https://github.com/joomla/framework.joomla.org/blob/master/src/Service/ApplicationProvider.php#L216-L263)

	$router = new Router();
	$router->get('/api/v1/packages', StatusControllerGet::class)
		->get('/api/v1/packages/:package', PackageControllerGet::class);


then to parse a route here [https://github.com/joomla/framework.joomla.org/blob/ddf9daaf6df42fd03a3f431d58c38dd2a4d3e557/src/WebApplication.php#L50-L60](https://github.com/joomla/framework.joomla.org/blob/ddf9daaf6df42fd03a3f431d58c38dd2a4d3e557/src/WebApplication.php#L50-L60)

    $route = $this->router->parseRoute($this->get('uri.route'));

    // Add variables to the input if not already set
    foreach ($route['vars'] as $key => $value)
    {
        $this->input->def($key, $value);
    }

    /** @var ControllerInterface $controller */
    $controller = $this->getContainer()->get($route['controller']);
    $controller->execute();

#### Phase 1
All routes will likely map to the same controller, this will then parse the routing file to get the possible parameters,
and then map the response from the model, to the defined response.

#### Phase 2
We expect that extensions will be allowed to specify their own controllers to the router alongside the route information

### Open API
Open API (formerly swagger) is an initiative to describe and document RESTful APIs. It allows the creation of standardised test tools and
documentation resources. [Version 2](https://github.com/OAI/OpenAPI-Specification/blob/master/versions/2.0.md) is used
by many major software projects, and [Version 3](https://github.com/OAI/OpenAPI-Specification/blob/rc1-version-bump/versions/3.0.md)
is reaching release candidate phase.

The Open API standard appears to be a good match for Joomla, it should allow easy creation of documentation through
various standard resources, it allows definitions of urls, request and response objects, and custom properties (which
we can use to control the model class required and the mapping between the request parameters and the state, and the
returned items and the state).
