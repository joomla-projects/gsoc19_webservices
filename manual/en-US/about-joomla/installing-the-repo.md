## How to install the webservices branch
Checkout the `lib_api` branch in this repository. Install Joomla as normal. You then need to discover install the 'Webservices - Content'
plugin (and publish it). Currently as the plugin name suggests webservices is only being worked on for com_content - however
it is being built in such a way that once it works for com_content it should be easy to apply across the rest of our
core components.

You can curl a list of articles with the following:

GET /api/index.php/article

you can also get a single article with:

GET /api/index.php/article/{article_id}

You may also POST, DELETE and PUT to the article ID endpoint, at the moment it is expected that these will end up with
permission failures as API Authentication has not been implemented yet.
