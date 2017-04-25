## Extensibility
As previously mentioned one of Joomla's biggest strengths is it's ability to install custom extensions from 
the [Joomla! Extensions Directory (JED)](https://extensions.joomla.org). This means that rather than hardcoded
json files like in many applications, extensions must be able to register their own endpoints.

Furthermore users may not wish to use the full amount of parameters that are provided by an extension. As a result
there must be the ability for a developer to disable extension routes or completely override the routes.

Overriding the routes should allow for the possibility that an extension may not distribute with a API routing file,
but that the developer of a single site can provide one.

### Implementation
At this stage there is not a agreed approach however some options are as follows:
 
 1. A routes.json file per extension (and a place to override the routes.json file)
 2. Endpoints as standard Joomla plugins
 3. A class/function that is called (like the traditional component router file)

The reality is that there will likely need to be some sort of combination of these approaches. Extensions will not want 
to have to provide a plugin per endpoint (this is going to be bloat for many extensions). However it's important that
we can use plugins to provide custom routes.

### Business Models

*Current Models*: Currently the state of the models is currently tightly coupled (populate state etc.) to web stuff and
is not channel-agnostic. However you can inject a custom state into the model, which should solve these problems.

As an example here is a rudimentary example for simple read operations. [https://github.com/mbabker/jdayflorida-app/tree/master/libraries/api/controller](https://github.com/mbabker/jdayflorida-app/tree/master/libraries/api/controller)

Following this train of thought, **JModels** are the best available business layer to integrate with existing extensions.

In the future a *Mini-Service Layer* could help to create a cleaner layer.

In this project, we will implement a simple serialization. Entity access level - Serialization from a model's getItem()
with a small layer to format the data in a format friendly to webservices.

Topics to be checked: Tags, Version history, JForms, Custom Fields, Rules, and Filters.

* *Interfaces (TBD)*:
  - JModelInterface
    - getItem
    - getItems  
    - ...  
