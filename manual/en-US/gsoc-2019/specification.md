## What we are looking to do in GSOC 2019
* Project Description: Continue work on the Joomla rest API endpoint (like API/) across all core components.
* Expected Results: All Joomla Core Components have a 
* Knowledge Prerequisite: PHP, RESTful Webservices. 
* Nice to have: Joomla MVC, Open API, Knowledge of APIs, experience with Laravels Eloquent Framework.
* Difficulty: Medium to Hard
* Mentors: Matias Aguirre [@fastslack](https://github.com/fastslack), Niels Braczek [@nibra](https://github.com/nibra), George Wilson [@wilsonge](https://github.com/wilsonge)

## Backstory & General Information
### Previous Work & General Webservices Specification
Before starting here we advise you read our webservices specification and GSOC 2017 and 2018 project writeups on the left. This
will give you vital backstory and forms the basis for this years project. If you feel something is missing let us know!
It probably is for others and means we can improve our documentation efforts!

### Issues with Joomla's Current Model System
Joomla's existing Model system uses the [following classes](https://github.com/joomla/joomla-cms/tree/4.0-dev/libraries/src/MVC/Model)
These combine so that for each list and item view in Joomla there is a Model class. However when considering an API this
becomes restrictive - for example currently in our articles list view for efficiency we don't retrieve the text of the
article. But in the API this is clearly required. 

Furthermore there are some architectural issues within these models - various parts of logic are split between the Model
classes and the [Table classes](https://github.com/joomla/joomla-cms/blob/staging/libraries/src/Table/Table.php). Furthermore
often the models directly access data from the request through `JModel::populateRequest` which should be done inside the
Controller for a clean separation.

## Targets for GSOC 2019
### Extend the API to all core components
Continue the rollout of the API to all core components:

* com_content
* com_contact
* com_newsfeeds
* com_weblinks 
* com_media (custom)
* com_menus
* com_categories
* com_contenthistory
* com_finder/com_search (read only as search and not CRUD)
* com_languages (both languages, content languages and overrides)
* com_messages
* com_modules/com_templates
* com_tags
* com_redirect
* com_plugins (edit settings only)
* com_config (edit settings only)


### Full Documentation
We need to document every components available endpoints and how to access them

### Bonus: Active Record Pattern Library
We expect that implementing core components will take up the majority of the time of the student. However if the student
does manage to implement this with time to spare, we will work on the gsoc 2018 project of building a new active record
pattern library for Joomla that can be found [on Github](https://github.com/joomla-framework/entities)
