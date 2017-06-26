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
- Easy to extend
- Doesn't take much work from developers to write

#### Cons
- Can be hard to implement

## Laravel REST framework
Laravel uses a similar approach to Django REST framework. A developer just specify a base route and matches it with the controller and the routing map is done. As simple as the following code snippet
```
Route::resource('photo', 'PhotoController');
```
This single route declaration creates multiple routes to handle a variety of RESTful actions on the photo resource. Likewise, the generated controller will already have methods stubbed for each of these actions, including notes informing you which URIs and verbs they handle.
A developer also can control which actions to have routes in a way easier than the way implemented in the Django REST framework. Like this code snippet.
```
Route::resource('photo', 'PhotoController',
                ['only' => ['index', 'show']]);

Route::resource('photo', 'PhotoController',
                ['except' => ['create', 'store', 'update', 'destroy']]);
```
They also have what is called **Nested Resources** which allows the developers to handle many to one relations routes. For example, the following route is for comments on photos.
```
Route::resource('photos.comments', 'PhotoCommentController');
```
This route will register a "nested" resource that may be accessed with URLs like the following: `photos/{photos}/comments/{comments}`.

For more info: https://laravel.com/docs/5.4/controllers#restful-resource-controllers

#### Pros
- Easy to extend
- Doesn't take much work from developers to write

#### Cons
- Can be hard to implement

## RoR REST framework
This is the source of inspiration for all other frameworks' implementations for RESTful routing.
In Rails, a resourceful route provides a mapping between HTTP verbs and URLs to controller actions. By convention, each action also maps to a specific CRUD operation in a database. A single entry in the routing file, such as:
```
resources :photos
```
creates seven different routes in your application, all mapping to the Photos controller:

|HTTP Verb|Path|Controller#Action|Used for|
|----|----|----|----|
GET|/photos|photos#index|display a list of all photos
GET|/photos/new|photos#new|return an HTML form for creating a new photo
POST|/photos|photos#create|create a new photo
GET|/photos/:id|photos#show|display a specific photo
GET |/photos/:id/edit|photos#edit|return an HTML form for editing a photo
PATCH/PUT|/photos/:id|photos#update|update a specific photo
DELETE|/photos/:id|photos#destroy|delete a specific photo

It uses the same concepts as other frameworks like **Nested Resources** , **Singular Resources**, etc.
For further knowledge about RoR visit this link: http://guides.rubyonrails.org/routing.html

## Slim REST framework
Slim framework is a micro framework which is simple and good to build RESTful APIs.
They build their routing within the app class as the developer specifies request by method, for example, if developer wants to add a route that handles only GET HTTP requests, it would be made as the following code.
```
$app = new \Slim\App();
$app->get('/books/{id}', function ($request, $response, $args) {
    // Show book identified by $args['id']
});
```
Such approach is simple to understand, implement and apply in extensions, but it would take much work to specify a route for each method.
## ASP<span>.</span>NET MVC framework
ASP<span>.</span>NET MVC framework uses a basic routing like Joomla framework's Router class. As in the following code snippet.
```
app.UseMvc(routes =>
{
   routes.MapRoute("default", "{controller=Home}/{action=Index}/{id?}");
});
```
There is a package that can be installed on top of ASP<span>.</span>NET to provide easier way to implement routing like this [package](http://restfulrouting.com/) .
It uses the same approach used by DRF, RoR and Laravel.

## Conclusion
Almost all frameworks implements the routing the same way except for few touches by the framework's builders. I think it's recommended to use the approach of Laravel as I think it's the most suitable approach that matches our project.