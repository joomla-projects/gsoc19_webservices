## What we are looking to do in GSOC 2018
* Project Description: Integrating a rest API endpoint (like API/) into the Joomla Core.
* Expected Results: Working on a new Active Record implementation for models with some sort of cross of ideas between JTable/JModel and more modern frameworks like Laravel's Eloquent.
* Knowledge Prerequisite: PHP, RESTful Webservices. 
* Nice to have: Joomla MVC, Open API, Knowledge of APIs, experience with Laravels Eloquent Framework.
* Difficulty: Medium to Hard
* Mentors: Matias Aguirre [@fastslack](https://github.com/fastslack), Niels Braczek [@nibra](https://github.com/nibra), George Wilson [@wilsonge](https://github.com/wilsonge)

## Backstory & General Information
### Previous Work & General Webservices Specification
Before starting here we advise you read our webservices specification and GSOC 2017 project writeup on the left. This
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

## Targets for GSOC 2018
### Fully Rebuild the Joomla Model System
We would like to rebuild our models with an Active Record Implementation. This will be a cross implementation of ideas from
our existing Model and Table implementations combined with ideas from [Laravel's Eloquent Framework](https://github.com/illuminate/database/tree/master/Eloquent)
which contains a much improved Active Record Implementation. The idea would be to deprecate the existing model system in Joomla 4,
and fully remove it in favour of your new implementation in Joomla 5.

#### Why not use Eloquent Directly?
We want to continue to be able to use our own database models from the Joomla Framework. As these do not require a PDO Driver
we are not able to directly consume Eloquent.

### Implement the New Model System
Convert com_content to use the new implementation. At this point there will have to be some investigation into what Controller
changes are required. We suspect the major change required to Controllers will be due to the removal of `JModel::populateRequest`
from the system - but others may well also be required.

### Full Documentation
We are going to be making one of the most significant changes to the vast Joomla Extension Ecosystem with the new model
system in nearly 10 years. It's very important that comprehensive documentation is available for people to use to help
migrations.

### Bonus: Authentication
We fully expect that building the new Model System will be a hugely time consuming task and will take up the entire project
If, however, the student does manage to implement this with time to spare, we will work on the authentication system for the
plugins. This will involve writing an authentication in a new plugin group to implement basic authentication.
