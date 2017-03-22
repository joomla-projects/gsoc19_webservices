# GSOC 2017: Webservices in Joomla 4

## RFC: Request for Comments

*Welcome to the Joomla! Google Summer of Code (GSoC) 2017 project: Webservices in Joomla 4!*

* Project Description: Integrating a rest API endpoint (like API/) into the Joomla Core. More information is going to follow in the next days.
* Expected Results: Working REST API for the core. Including com_content as the reference implementation.
* Knowledge Prerequisite: PHP, RESTful Webservices. 
* Nice to have: Joomla MVC, Swagger, Knowledge of APIs.
* Difficulty: Medium to Hard
* Mentors: Matias Aguirre [@fastslack](https://github.com/fastslack), Anibal Sanchez [@anibalsanchez](https://github.com/anibalsanchez), George Wilson [@wilsonge](https://github.com/wilsonge)
* Official repository: [joomla-projects/gsoc17_webservices](https://github.com/joomla-projects/gsoc17_webservices)

* **Status: Draft**

## Purpose

This document is intended to describe the current state of the general conversation about the scope of the *GSOC 2017: Webservices in Joomla 4* project.

Since there were several previous attempts to implement *Webservices in Joomla*, the focus of this particular project is to achieve a working project to provide a REST interface for Joomla 4. Joomla 4 will be released with this project as a tool for the community benefit. For more information about a REST API: [Web API Design - Crafting Interfaces that Developers Love](https://pages.apigee.com/rs/apigee/images/api-design-ebook-2012-03.pdf).

## MVP: Minimum Viable Product

### Project Definitions

This is the main list of features for the MVP:

#### 1. REST API 

URL Route: The REST API will live in /api.

a. Calls  Joomla Content (com_content)
  - /api for Articles
  - List Articles (/api/v1/articles)
  - Retrieve a Article (/api/articles/v1/999)
  - Create a Article
  - Update a Article
  - Delete a Article

> Api/v1/ should be defined by the extension routes (v1 is not static). Versioning is probably going to be something defined at a component level.

b. Responses
  - Entity access level - Serialization from a model's getItem()
  - Full serialization (E.g. Sylius or FriendsOfSymfony/FOSRestBundle) - out of scope / milestone 2 or 3
  - Column by column - out of scope / milestone 2 or 3

#### 2. Controllers - *The micro-framework to create web service protocol stack*

##### a. Development Alternative: Joomla! Framework

Joomla! has evolved for more than 10 years, powered by MVC, developed to solve its specific requirements to create an award-winning content management system (CMS), which enables you to build web sites and powerful online applications. 

In practice, these are the key classes that support Joomla! MVC:
  - JApplicationSite and JApplicationAdministrator
  - JControllerLegacy
  - JModelLegacy and JTable
  - JRouter and JMenu
  - JFactory

Joomla! MVC is currently oriented to support Joomla! CMS. In its evolution, it has perfected a specific way to solve the CMS challenges, including a complex content model to support Category Management, JForms, Custom Fields, Rules, and Filters. *This implementation cannot be easily tailored to support a REST interface*.  

JControllers: existing component controllers are not designed for WS. So there is a need for specialised controllers for REST versus full HTML. This is a core infrastructure problem that has to be solved in general (not related to just the WS project).

Additionally, we have [Joomla! Framework](https://framework.joomla.org/), a new PHP framework for writing web and command line applications without the features and corresponding overhead found in the Joomla! Content Management System (CMS). At this time, Joomla 4 is coming with Joomla! Framework:

- php >=5.5.9
- joomla/application
- joomla/crypt
- joomla/data
- joomla/di
- joomla/event
- joomla/http
- joomla/image
- joomla/ldap
- joomla/registry
- joomla/session
- joomla/string
- joomla/uri
- joomla/utilities
- ircmaxell/password-compat
- leafo/lessphp
- paragonie/random_compat
- phpmailer/phpmailer
- symfony/polyfill-php55
- symfony/polyfill-php56
- symfony/yaml    

Sample code to define routes in Joomla! Framework: [https://github.com/joomla/framework.joomla.org/blob/master/src/Service/ApplicationProvider.php#L227-L230](https://github.com/joomla/framework.joomla.org/blob/master/src/Service/ApplicationProvider.php#L227-L230)

		$router = new ContainerAwareRestRouter($container->get(Input::class));
		$router->setControllerPrefix('Joomla\\FrameworkWebsite\\Controller\\Api\\')
			->addMap('/api/v1/packages', 'StatusController')
			->addMap('/api/v1/packages/:package', 'PackageController');

Possible /api folder structure. New J-WS Classes, based on Joomla! Framework:

		/api/index.php
		/api/router.php (routes.json - RestRouter)
		/api/controller (initial working build - generic controller)
			component controllers - milestone 2 or 3
			extensionname/Controller/Api or WebService
		/api/includes	      

###### [Joomla! Framework Sample Applications](#jf-samples)

To guide the implementation, we have several sample applications, poewered by Joomla! Framework:

* https://github.com/joomla-framework - Lot of useful information into libraries README [https://github.com/joomla-framework](https://github.com/joomla-framework)
* https://framework.joomla.org - Application powering the internet home of the Joomla! Framework. [https://github.com/joomla/framework.joomla.org](https://github.com/joomla/framework.joomla.org)
* https://help.joomla.org - Application powering the Joomla! Help Screen proxy serving help screens to Joomla! installations. [https://github.com/joomla/help.joomla.org](https://github.com/joomla/help.joomla.org)
* Joomla Stats. Server for collecting environment stats for Joomla Installations. [https://github.com/joomla/statistics-server](https://github.com/joomla/statistics-server)
* https://issues.joomla.org - Issue tracking application extending GitHub's issues and pull requests for the Joomla! project. [https://github.com/joomla/jissues](https://github.com/joomla/jissues)
* jUpgradeNext. An application using Joomla! Framework with DI. [https://github.com/matware-lab/jUpgradeNext/blob/master/src/CliApplication.php](https://github.com/matware-lab/jUpgradeNext/blob/master/src/CliApplication.php)
* How to register providers with [https://github.com/matware-lab/jUpgradeNext/blob/master/src/CliApplication.php#L121](https://github.com/matware-lab/jUpgradeNext/blob/master/src/CliApplication.php#L121)

##### b. Development Alternative: 3-Party Micro-framework 

As an alternative, nowadays, several popular micro-frameworks can solve the project requirements. Following this argument, these are some suitable third-party micro-frameworks:

| Feature | Slim Framework | Lumen (Laravel) | Silex (Symfony)
| ------- | -------------- | --------------- | ----------------|
| php     | 5.5.0          | 5.6.4           |        5.5.9    |
| Dependency Container | Pimple ([or 3rd-party DI](https://www.slimframework.com/docs/concepts/di.html)) | Laravel | Pimple |
 
These alternatives offer a solution to implement the web service protocol stack. However, they bring new challenges:

- Addition of a new stack to Joomla! ecosystem (that it is not managed by Joomla! community).
- Duplication
  
> A third party REST library requires an entirely separate infrastructure, to boot the application, trigger our plugins, models, deal with figuring out how to get routes mapped, etc.

For instance, the plugin system is important to allow data manipulation. JForms has events to enhance forms. (E.g. RSS Feeds don't have any event associated with plugins).

#### 3. Business Models

- *Current JModels*: The state of the models is currently tightly coupled (populate state etc.) to web stuff. Not channel-agnostic. PRO: They are thoroughly tested. For example: High level hacks for simple read operations. [https://github.com/mbabker/jdayflorida-app/tree/master/libraries/api/controller](https://github.com/mbabker/jdayflorida-app/tree/master/libraries/api/controller)

Following the previous arguments, in both cases, **JModels** are the best available business layer to integrate the upper layers.

In the future a *Mini-Service Layer* could help to create a clean layer. E.g. Article management via JTable, featured and frontend tables.

In this project, we will implement a simple serialization. Entity access level - Serialization from a model's getItem()

Topics to be checked: Tags, Version history, JForms, Custom Fields, Rules, and Filters.

* *Interfaces (TBD)*:
  - JModelInterface
    - getItem
    - getItems  
    - ...  

##### 4. Extensibility

Other extensions must be able to add new entry points. REST API for Joomla Contacts (com_contact)

  - /api for Contacts
  - List Contacts (/api/v1/contacts)
  - Retrieve a Contact (/api/contacts/v1/999)
  - Create a Contact
  - Update a Contact
  - Delete a Contact
  - routes.json or routes.extensionname.xml

##### 5. User Interface

Optional. To configure the webservice.

##### 6. API Key Authentication

Authentication based on a general token. Generate an API key per user (in com_users for us) and use this in the header for auth. A general API Key.

### Development

- *External project from J4*. PRO: Freedom to develop and propose core changes.
- Inclusion in the Core: Time constraints and harder to be accepted. Hard to evolve.

### Unit Tests

The project must include tests.

- [Running Automated Tests for the Joomla CMS](https://docs.joomla.org/Running_Automated_Tests_for_the_Joomla_CMS)

### Nice to have

* User Interface to configure
* Backwards compatibility with J3

### Out of the scope

* ACL at field level
* OAUTH
* Service Layer

## Comparable Products

* Joomla! Downloads - API [https://downloads.joomla.org/api-docs/](https://downloads.joomla.org/api-docs/)
* Joomla! Downloads - Private Repo [https://github.com/joomla/downloads.joomla.org](https://github.com/joomla/downloads.joomla.org)
* WP REST API v2 Documentation - [http://v2.wp-api.org/](http://v2.wp-api.org/). [REST API Handbook](https://developer.wordpress.org/rest-api/reference/posts/)
* Drupal 8 APIs - RESTful Web Services API - [https://www.drupal.org/docs/8/api/restful-web-services-api/restful-web-services-api-overview](https://www.drupal.org/docs/8/api/restful-web-services-api/restful-web-services-api-overview)
* Laravel RESTful Resource Controllers - [https://laravel.com/docs/5.1/controllers#restful-resource-controllers](https://laravel.com/docs/5.1/controllers#restful-resource-controllers)

## Other References

* **Joomla! Documentation - Web Services Working Group** [https://docs.joomla.org/Web_Services_Working_Group](https://docs.joomla.org/Web_Services_Working_Group) - Contacts Ashwin Date [@coolbung](https://github.com/coolbung) and Chris Davenport [@chrisdavenport](https://github.com/chrisdavenport)
* **Mar 12, 2016** - GitHub - chrisdavenport/webservices: Webservices working group repository* - [https://github.com/chrisdavenport/webservices](https://github.com/chrisdavenport/webservices)
* **Aug 31, 2013** - GitHub - chrisdavenport/j3-rest-api: REST API for Joomla 3.x - [https://github.com/chrisdavenport/j3-rest-api](https://github.com/chrisdavenport/j3-rest-api)
* **May 2, 2016** - GitHub - chrisdavenport/service: Experimental Service Layer for Joomla 3.x - [https://github.com/chrisdavenport/service](https://github.com/chrisdavenport/service)
* **Three Years with the WordPress REST API** - K. Adam White. [https://bocoup.com/blog/three-years-with-the-wordpress-rest-api](https://bocoup.com/blog/three-years-with-the-wordpress-rest-api) *... the answer was clear: there’s a lot of good features in WordPress, but the code foundation upon which they are built is uneven. The REST API endpoints provide a new foundation for future core feature development, a facade layer that abstracts the inconsistencies of the past...*

### Vendor Implementations

* Webservices in redCORE - [http://redcomponent-com.github.io/redCORE/?chapters/webservices/overview.md](http://redcomponent-com.github.io/redCORE/?chapters/webservices/overview.md)
* Joomla REST API - techjoomla/Joomla-REST-API - [https://github.com/techjoomla/Joomla-REST-API](https://github.com/techjoomla/Joomla-REST-API)
* RESTful API plugins - [appcarvers/com_api-plugins](https://github.com/appcarvers/com_api-plugins)

## Community Channels

* Joomla GSoC 2017 - Google Group: https://groups.google.com/forum/#!forum/jgsoc2017
* Joomla GSoC 2017 - Google Group Topic: **RFC - GSOC 2017: Webservices in Joomla 4** https://groups.google.com/forum/#!topic/jgsoc2017/RYodqGfr8Hs
* GSoC 2017 Idea's-Mentors List: https://docs.google.com/spreadsheets/d/1JnpspX_Uwh1Dk7iYqoQvtOVieSuRjP366PmzQPOYfHk/edit#gid=0
* Glip Channel: GSoC 17 Webservices 

## Definitions for the students

* What should be the place for a student to start with, (pointing them to the right place)
* Required Skills to join the project
* 1 Student Slot. Optional 1 more slot if it could be defended.

## Important Upcoming Dates

* Now - March 20: Proactive students will reach out to you and ask questions about your ideas list and receive feedback from your org so they can start crafting their project proposals.
* March 20 - April 3 16:00 UTC: Students will submit their draft proposals through the program website for you to give solid feedback on.
* April 3 - 16: Review all submitted student proposals with your org and consider how many you want to select and how many you can handle. Decide on the minimum/maximum number of student slots to request.
* April 17, 16:00 UTC: Deadline to submit slot requests (OAs enter requests)
* April 19, 16:00 UTC: Slot allocations are announced by Google
* April 19 - 24 16:00 UTC : Select the proposals to become student projects. At least 1 mentor must be assigned to each project before it can be selected. (OAs enter selections)
* April 24 - May 4: Google Program Admins will do another review of student eligibility
* May 4: Accepted GSoC students/projects are announced
* May 4 - 29: Community Bonding Period
* May 29: Deadline to notify Google Admins of an inactive student that you wish to be removed from the program
* May 30: Coding begins
* June 26-30: First evaluation period - mentors evaluate their students and students evaluate mentors
* July 24 - 28: Second evaluation period - mentors evaluate students, students evaluate mentors
* August 21- 29: Students wrap up their projects and submit final evaluation of their mentor
* August 29 - September 5: Mentors submit final evaluations of students
* September 6: Students passing GSoC 2017 are announced
