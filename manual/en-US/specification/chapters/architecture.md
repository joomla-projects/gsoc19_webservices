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
Initialization  |          | ✔
- Container     | ✔         | 
- CMS           |            | ✔
URLs and Routing         | ✔         | 
API Authentication  | ✔         | 
- Global API Key  | ✔         | 
- Extension Auth  |          | ✔
Extensibility   | ✔         | 
- Controllers     | ✔         |
- Controller Overrides |       | ✔
- Models          |            | ✔
- Tables (DBAL)   |            | ✔
- Serialization   | ✔         | 
-- General       | ✔         |  
-- Extension Param  |           | ✔
User Interface  |           | ✔

 ### Required Changes to the CMS and the extension ecosystem

 To provision the API, these are the only code changes required to the CMS and the extension ecosystem.

 - Routes Definition
 - API Authentication - Extension Specific
 - Controller Overrides
 - Serialization - Extension Parametrization
