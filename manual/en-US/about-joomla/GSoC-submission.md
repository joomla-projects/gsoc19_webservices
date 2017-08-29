# GSoC Submission
This page to record the progress made during Google Summer of Code program in the last three months.

## About the project
This project is about integrating a rest API endpoint (like API/) into the Joomla 4 Core. As web services became a necessity in today’s web development world. It helps add other dimensions to the power of websites for example: a new website can sell their articles for other developers as APIs, also they can use Joomla! API to build a mobile application for their news and articles to help them reach more audiences.

As Joomla is one of the most famous CMSs in the world, with millions of websites that use it, It would be great that Joomla have the capability to provide APIs for websites owners.

As it's a big project, Joomla GSoC teams decided to give it to two students, [Mohamed Karam](https://github.com/muhakh) and [Altay Adademir](https://github.com/cokencorn).

For further description, check this [article](https://community.joomla.org/gsoc-2017/3129-webservices-in-joomla-gsoc-2017.html).
## Work done
The following list is a list of main parts created for the project:
- Api Application
- Api Router
- Api Document
- Api Controller
- Api Views
- com_content component as the reference implementation

### Api Application
Joomla framework calls a stand alone part of a project an application, like most of the frameworks.
A Joomla application is responsible for handling the request, choose a suitable router, route the request to a particular component and then execute the component's logic.
Everything starts with a request! Joomla’s API (ApiApplication to be exact) executes when a request is received from the entry point of the API which is “<root>/api”. The request is then parsed and routed in the ApiApplication.
Example API requests for `com_content` is given below.
- List Articles (/api/v1/articles)
- Single Article (/api/articles/v1/999)
- Retrieve an Article (GET request)
- Create a Article (POST request)
- Update an Article (POST request)
- Delete an Article (DELETE request)
The implementation of this application can be found [here](https://github.com/joomla-projects/gsoc17_webservices/blob/lib_api/libraries/src/Application/ApiApplication.php)

### Api Router
Routing is the process of examining the request environment to determine which component should receive the request. We used the [router library](https://github.com/joomla-framework/router/blob/2.0-dev/docs/overview.md) included in Joomla framework. Simply, the API application has an ‘ApiRouter’ object  and when the application receives a request, it triggers an event called ‘onBeforeApiRoute’ where component developers can build plugins that catch that event and build a routing map for the component. After that, the application uses the router’s object to parse the URL according to the map and the application dispatches the component corresponding to the parsed URL.
When the components catch the routing event, they can access to the router object that is passed to the routing event. The component can then add any routes to the routing map of the router object.
The implementation of this router can be found [here](https://github.com/joomla-projects/gsoc17_webservices/blob/lib_api/libraries/src/Router/ApiRouter.php)

### Api Controller
This controller gives a component developer the implementation to handle a basic CRUD for the component.
The implementation of this controller can be found [here](https://github.com/joomla-projects/gsoc17_webservices/blob/lib_api/libraries/src/Controller/Api.php)

### Api Views
We created two views for the API, list view and item view. These views are for retrieving data and push them into the Api document which would be returned as a response to the client.
The implementation of these views can be found [here](https://github.com/joomla-projects/gsoc17_webservices/blob/lib_api/libraries/src/View/ListJsonView.php) and [here](https://github.com/joomla-projects/gsoc17_webservices/blob/lib_api/libraries/src/View/ItemJsonView.php)

### com_content component as the reference implementation
This part was hard, as we didn't want the component developers to repeat themselves by creating models for APIs which can be similar to their site or administrator models, we made it possible for them to use (by default) the models of these apps.
It still needs more work but it works fine for GET requests.
The implementation of this component can be found [here](https://github.com/joomla-projects/gsoc17_webservices/blob/lib_api/api/components/com_content)

## More Work
This project still needs more work, which can be found here in these [issues](https://github.com/joomla-projects/gsoc17_webservices/issues).
We hope after finishing these issues, this project is merged to Joomla 4 core.

## Our contributions
- [These are Mohamed Karam's contributions to the project](https://github.com/joomla-projects/gsoc17_webservices/commits/lib_api?author=muhakh)
- [These are Altay Adademir's contributions to the project](https://github.com/joomla-projects/gsoc17_webservices/commits/lib_api?author=cokencorn)
