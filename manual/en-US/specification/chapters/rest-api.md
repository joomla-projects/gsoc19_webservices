## Rest API

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
