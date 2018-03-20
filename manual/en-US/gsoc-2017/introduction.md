## Information on GSOC 2017
* Project Description: Integrating a rest API endpoint (like API/) into the Joomla Core.
* Expected Results: Working REST API for the core. Including com_content as the reference implementation.

### Results
The students worked in this repository in the `lib_api` branch to create a new application for core that included the
basic requirements for the API application in Joomla. The students report on their work can be found on the student submission
page [here](gsoc-2017/GSoC-2017-submission.md).

### What is left to do
#### Authentication
The [API Authentication Page](specification/chapters/api-authentication.md) required Basic Authentication as a mechanism
for authentication. This would require a new plugin section, with the appropriate permissions setup. These are required
to be able to use POST, PUT and DELETE requests. On basic testing com_content (without authentication) appears to work
for DELETE requests - so there should be an easy way of testing this on implementation.

#### PUT and POST Requests
For more information see [this GitHub issue](https://github.com/joomla-projects/gsoc18_webservices/issues/51)

#### Remove various hacks done for base implementation
- [Remove com_content dependency in library code](https://github.com/joomla-projects/gsoc18_webservices/issues/48)
- [Revert hacks made to ApiMvcFactory](https://github.com/joomla-projects/gsoc18_webservices/issues/54)

#### Content Plugins
Content plugins are often run in the View-Model classes at the moment which means that they aren't being run on the API
content. For more information see [this GitHub issue](https://github.com/joomla-projects/gsoc18_webservices/issues/55)

#### Joomla's Model System
This turns out to be a more significant pain point than originally anticipated. Part of the reason for this is that we
have separate list and item views, where often the list views only retrieve a subset of data. This is an issue where in
list API views you need to, for example retrieve the full article object. The best way to deal with this is to have a single
model for both list and item views, but clearly this was going beyond the scope of this GSOC Project. As a temporary measure
we have added some extra returns

It would also be good to start to have a clear way of defining what data should be returned by the API at this level that
extensions can work with - for example we should be consistent on whether we return things like `asset_id` by default or not
