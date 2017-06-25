There are many approaches reached by frameworks that supports RESTful APIs. Each approach has its pros and cons.
In this page, we will speak about them and make a comparison between these different approaches to reach the most suitable approach for our implementation for RESTful APIs for Joomla! CMS.
## Django REST framework
In this framework, developers specify base route for a set of controllers (called ViewSet in Django) and the framework takes care of the rest of the job.

There are three types of routers SimpleRouter, DefaultRouter, and CustomRouter.
#### SimpleRouter
Let's consider a controller for articles. Its base route would be `/article/`. This router includes routes for the standard set of `list`, `create`, `retrieve`, `update`, `partial_update` and `destroy` actions.
It's as simple as the following piece of code.
```
router = routers.SimpleRouter()
router.register(r'articles', ArticleViewSet)
urlpatterns = router.urls
```
To go further these methods in controllers, python decorators can be used as in this [link](http://www.django-rest-framework.org/api-guide/routers/#extra-link-and-actions) 
#### DefaultRouter
This router is similar to `SimpleRouter` , but additionally includes a default API root view, that returns a response containing hyperlinks to all the list views. It also generates routes for optional .json style format suffixes.

#### CustomRouter
From its name, It's used to write developers' own custom routers, for example, if a developer wants to to route an action  "list or update" and not to route the others, he/she can do that easily by extending the SimpleRouter or the DefaultRouter.

For more info: http://www.django-rest-framework.org/api-guide/routers/

#### Pros
- Doesn't take much work for simple business logic
- Easy to extend
- Doesn't take much work from developers to write
#### Cons
- Can be hard to implement
## RoR REST framework
## Laravel REST framework
## Slim REST framework
## ASP<span>.</span>NET framework
