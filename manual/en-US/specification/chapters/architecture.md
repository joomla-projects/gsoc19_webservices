## Webservices in Joomla 4! - Architecture

To solve the challenges faced by the Webservices in Joomla 4! implementation, we are going to follow these guidelines:

### Webservices Entry Point

The project will have its own entry point, URL: /api/index.php.

This page will load the context to initialize the  Webservices stack and the integration with the CMS.

### Joomla Framework Organization

The project will implement the upper layers on Joomla Framework and integrate the lower layers of the Joomla! CMS. In this way, the project will incorporate the benefits of Joomla Framework, CMS and the extension ecosystem. The aim of this definition is to avoid code changes on the CMS and the extension ecosystem.

In practice, this is how the Webservices stack will be organized:

Topic           | Framework | CMS
---------       | ----------| ----------
Initialization  | ✔         | 
Routing         | ✔         | 
Authentication  | ✔         | 
Extensibility   | ✔         | 
Controllers     | ✔         |
Controller Overrides |       | ✔
Models          |            | ✔
Tables          |            | ✔
Serialization   | ✔         | 
User Interface  |           | ✔

 ### Required Changes to the CMS and the extension ecosystem

 To provision the API, these are the only code changes required to the CMS and the extension ecosystem.

 - Routes Definition
 - API Authentication


Joomla! has evolved for more than 10 years, developed to solve its specific requirements to create an award-winning content management system (CMS), which enables you to build web sites and powerful online applications. 
Joomla 4 will be the 5th major release of the Joomla CMS (following 1.0, 1.5, 2.5 and 3)

### Joomla's MVC layer
Since Joomla 1.5, Joomla has shipped with a Model, View, Controller (MVC) layer. This allows separation of logic between
the sanitation of data from the request/session, the database layer and the view outputting.
Joomla! MVC is currently oriented to support Joomla! CMS. In its evolution, it has perfected a specific way to solve the CMS challenges, including a complex content model to support Category Management, Forms, Custom Fields, Rules, and Filters.
However the implementation was designed with HTML pages in mind and cannot be easily tailored to support a REST interface.  

There are several important classes that support the Joomla! MVC:
  - JApplicationSite and JApplicationAdministrator (the main Joomla application backbone)
  - JController
  - JModel and JTable
  - JRouter and JMenu (for routing)
  - JFactory (slowly being replaced by a Dependency Injection Container from Joomla 4 onwards)
  - JView - primarily designed for HTML output but can be used for other outputs (e.g. com_modules json view)

Note that JController, JModel and JView are called JControllerLegacy, JModelLegacy and JViewLegacy in the Joomla 3.x structure.


### The Joomla Framework
The [Joomla! Framework](https://framework.joomla.org/) is a PHP framework for writing web and command line applications that can be used outside of the Joomla! CMS.

Most of the framework packages are originally designed for the CMS and have since been abstracted to work in any PHP environment with framework interoperability in mind.

Where possible we would like to continue using our Framework packages over those from 3rd party sources.

### The need for webservices and the challenges faced
As the web has involved over the last 5-10 years, building API's to accompany a website has become an industry best practice

Joomla's challenges are unique in that we have a hugely popular extension ecosystem that is at the core of our community.
Building an API requires integration with all these extensions and requiring extension developers to do the least work
possible without compromising code quality.
