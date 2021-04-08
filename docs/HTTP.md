## HTTP-based application

Files related to an HTTP application can be found in the `public`, `src/Http` and `templates` folders.

The `public` folder contains the `index.php` file, which is the "entry" point for every incoming HTTP request 
(TOTO: link to an article about the "Front Controller" design pattern) and in 99% of cases should not be modified.

The `src/Http` folder contains the "meat" of an HTTP application. Here you will find a couple of things:

* HTTP actions, located in the `src/Http/Action` folder,
* custom HTTP requests, located in the `src/Http/Request` folder,
* HTTP middleware, located in the `src/Http/Middleware/Guard` folder,
* HTTP routes definitions, in the `src/Http/Routing/routes.php` file.

The `templates` folder contains the template files, a.k.a. views.

Here's how it all ties together.

### The gist

Once a browser sends a HTTP request, the Web server will forward it to the `public/index.php` file. This file will call
the router and pass it the requested URL. The router will check if the URL matches any of the routes defined in the
`routes.php` file. If it finds a matching route definition there, the `execute()` method of the HTTP action class will 
be called. 

If there were any middleware declared in the route definition, those will be called, in order, prior to calling the 
action. In the end, the action generates a response, which is then sent back to the browser.

### Routes definitions

Routes definitions are kep in the `routes.php` file. It's a simple PHP file, which just returns an array of routes
definitions.

Each element of the array, i.e. a **route definition** is an array, consisting of 3 or 4 elements:

* the HTTP method name - currently only `GET` and `POST` values are supported,
* the URI, e.g. `/`, `/foo` or `/product/show`,
* the class name of the HTTP action,

These 3 elements are required in a route definition. The 4th element is optional - it's an array of HTTP middleware, in
the form of a list of middleware class names. If one does not wish for the route definition to utilize any middleare,
one should simply omit that last element in the route definition or declare it as an empty array (`[]`).

Here's an example of a route definition with no middleware:

```php
['GET', '/', DummyAction::class],
```

And here's an example of a route definition with one middleware:

```php
['GET', '/', DummyAction::class, [DummyGuard::class]],
```

### HTTP Actions

Every defined route should have an HTTP action class defined. These are like the ever popular Controllers, except where 
a Controller has separate methods for different routes, an HTTP action only has one method.

There are a couple of requirements every HTTP action class must meet:

* it must extend the `\Noctis\KickStart\Http\Action\AbstractAction` class,
* it must have a public method called `execute`, which returns a `Response` object,
* any of the action's dependencies should be injected through the `execute` method's signature, eg.:

```php
public function execute(DummyRequest $request, DummyService $dummyService): Response
{
    // ...
}
```

**Although possible, it is not recommended to inject dependencies into the HTTP action through its constructor!**

### Requests

If the HTTP action needs to get some data from the incoming HTTP request, one may define a request class for said 
action. The request class should be created in the `src/Http/Request` directory and must extend the
`\Noctis\KickStart\Http\Request\AbstractRequest` class. 

One may find an example of such request class in the `src/Http/Request/DummyRequest.php` file.

### Responses

Usually one wants the HTTP action to return a response with some generated HTML. To do that, one should call its
`render()` method at the end of the action's `execute()` method:

```php
public function execute(): Response
{
    // ...
    
    return $this->render('dummy.html.twig');
}
```

The `render()` method's first argument is the name of the template file which should be rendered. `Kickstart`'s
rendering engine of choice is [Twig 3.x](https://twig.symfony.com/doc/3.x/). The template file should exist in the
`templates` directory (next to the `src` dir).

If one wishes to pass some extra arguments to the template file, one can do that through `render()`'s second argument, e.g.:

```php
use Symfony\Component\HttpFoundation\Response;

public function execute(): Response
{
    // ...
    
    return $this->render('dummy.html.twig', ['name' => 'Marcus']);
}
```

If one wishes to return a redirection response instead, one should call the `redirect()` method, while also slightly
altering the `execute()`'s signature:

```php
use Symfony\Component\HttpFoundation\RedirectResponse;

public function execute(): RedirectResponse
{
    // ...
    
    return $this->redirect('/');
}
```

### Middleware

If one wishes for a piece of code to be executed before a HTTP action's `execute()` method is called, for example
to check if a user is actually logged in, one may do so by creating a middleware class and attaching it to the route
definition.

The middleware defined in the route definition are executed before the HTTP action is called, in the order they have
been defined in the route definition. A middleware **may** generate response object and return it. In such case, the HTTP
action will NOT be called and the response generated by the middleware object will be returned to the browser.

If you wish to learn more about PHP middleware, you will find more information about it 
[here](https://phil.tech/2016/why-care-about-php-middleware/).
