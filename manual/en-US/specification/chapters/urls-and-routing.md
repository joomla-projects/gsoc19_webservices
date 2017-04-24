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
Entity access level - Serialization from a model's getItem() - with appropriate formatting (e.g. custom fields should be moved as top level items etc.)

Note however that we do still expect responses to conform to the Richardson Maturity Model 

#### Phase 2
Either:
  - Full serialization (E.g. Sylius or FriendsOfSymfony/FOSRestBundle)
  - Column by column


### The Joomla Framework Router
The most important framework package we will start to use as part of the webservice project is the Framework routing package (version 2)

This router is designed to map a URL to a controller. You can find sample code to define routes in Joomla! Framework at: [https://github.com/joomla/framework.joomla.org/blob/master/src/Service/ApplicationProvider.php#L227-L230](https://github.com/joomla/framework.joomla.org/blob/master/src/Service/ApplicationProvider.php#L227-L230)

	$router = new ContainerAwareRestRouter($container->get(Input::class));
	$router->setControllerPrefix('Joomla\\FrameworkWebsite\\Controller\\Api\\')
		->addMap('/api/v1/packages', 'StatusController')
		->addMap('/api/v1/packages/:package', 'PackageController');

