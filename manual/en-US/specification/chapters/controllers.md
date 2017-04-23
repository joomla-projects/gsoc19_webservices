## Controllers

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
