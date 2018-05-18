## How to install the webservices branch
Checkout the `lib_api` branch in this repository. Install Joomla as normal. You then need to discover install the 'Webservices - Content'
plugin (and publish it). Currently as the plugin name suggests webservices is only being worked on for com_content - however
it is being built in such a way that once it works for com_content it should be easy to apply across the rest of our
core components. You must authenticate with basic authentication. For the examples below we shall use a username of
'admin' and a password of 123

You can curl a list of articles with the following:

`curl -H 'Authorization: Basic YWRtaW46MTIz' -X GET /api/index.php/article`

you can also get a single article with:

`curl -H 'Authorization: Basic YWRtaW46MTIz' -X GET /api/index.php/article/{article_id}`

You can delete an article with:

`curl -H 'Authorization: Basic YWRtaW46MTIz' -X DELETE /api/index.php/article/{article_id}`

You can create an article with:

`curl -H 'Authorization: Basic YWRtaW46MTIz' -X POST -H "Content-Type: application/json" /api/index.php/article -d '{"title": "Just for you", "catid": 64, "articletext": "My text", "metakey": "", "metadesc": "", "language": "*", "alias": "tobias"}'`	

Finally you can update an article with:

`curl -H 'Authorization: Basic YWRtaW46MTIz' -X PUT -H "Content-Type: application/json" /api/index.php/article/{article_id} -d '{"title": "Just for you part 2", "catid": 64}'`	
