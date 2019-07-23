## How to install this repo webservices branch
> NOTE: As of the start of GSOC 2019 you might want to use the main Joomla 4 repo Nightly builds or Alpha 7 or later for
        a stable version of the code.

Checkout the `api_components` branch in this repository. Install Joomla as normal. Please see https://docs.joomla.org/J4.x:Setting_Up_Your_Local_Environment.
Currently we're not supporting all core components however the API is slowly being extended to cover the rest of our components.
You must authenticate with basic authentication. For the examples below we shall use a username of 'admin' and a password of 123

You can curl a list of articles with the following:

`curl -H 'Authorization: Basic YWRtaW46MTIz' -X GET /api/index.php/v1/article`

you can also get a single article with:

`curl -H 'Authorization: Basic YWRtaW46MTIz' -X GET /api/index.php/v1/article/{article_id}`

You can delete an article with:

`curl -H 'Authorization: Basic YWRtaW46MTIz' -X DELETE /api/index.php/v1/article/{article_id}`

You can create an article with:

`curl -H 'Authorization: Basic YWRtaW46MTIz' -X POST -H "Content-Type: application/json" /api/index.php/v1/article -d '{"title": "Just for you", "catid": 64, "articletext": "My text", "metakey": "", "metadesc": "", "language": "*", "alias": "tobias"}'`	

Finally you can update an article with:

`curl -H 'Authorization: Basic YWRtaW46MTIz' -X PUT -H "Content-Type: application/json" /api/index.php/v1/article/{article_id} -d '{"title": "Just for you part 2", "catid": 64}'`	
