# HTTP-based application

Files related to an HTTP application can be found in the `public`, `src/Http` and `templates` folders.

The `public` folder contains the `index.php` file, which acts as an "entry" point for every incoming HTTP request 
(TODO: link to an article about the "Front Controller" design pattern) and in 99% of cases should not be modified.

The `src/Http` folder contains the "meat" of an HTTP application. Here you will find a couple of things:

* HTTP actions, located in the `src/Http/Action` folder,
* custom HTTP requests, located in the `src/Http/Request` folder,
* HTTP middleware, located in the `src/Http/Middleware/Guard` folder,
* HTTP routes definitions, in the `src/Http/Routing/routes.php` file.

The `templates` folder contains the template files, a.k.a. views.

Here's how it all ties together.

## The Gist

Once a browser sends an HTTP request, the WWW server will forward it to the `public/index.php` file. This file will call
Kickstart's router and pass it the requested URL, along with any query params. The router will check if the URL matches 
any of the routes defined in the `src/Http/Routing/routes.php` file. If it finds a matching route definition there, 
the `execute()` method of the referenced HTTP action class will be called. 

If there were any middleware declared in the route definition, those will be called, in order, prior to calling the 
action. In the end, the action generates a response, which is then sent back to the browser.

## Routes Definitions

Routes definitions are kept in the `src/Http/Routing/routes.php` file. It's a simple PHP file, which returns an array of 
**route definitions**.

Each element of the array, i.e. a route definition is an array, consisting of 3 or 4 elements:

* the HTTP method name - currently only `GET` and `POST` values are supported,
* the URI, e.g. `/`, `/foo` or `/product/show`,
* the class name of the HTTP action,

These 3 elements are required in a route definition. The 4th element is optional - it's an array of HTTP middleware, in
the form of a list of middleware class names. If you do not wish for the route definition to utilize any middleware,
you should simply omit that last element in the route definition or declare it as an empty array (`[]`).

Here's an example of a route definition with no middleware:

```php
['GET', '/', DummyAction::class],
```

And here's an example of a route definition with one middleware:

```php
['GET', '/', DummyAction::class, [DummyGuard::class]],
```

## HTTP Actions

Every defined route should have an HTTP action class defined. These are like the ever popular Controllers, except where 
a Controller has separate methods for different routes, an HTTP action only has one method, called `execute()`.

There are a couple of requirements every HTTP action class must meet:

* it must extend the `Noctis\KickStart\Http\Action\AbstractAction` abstract class,
* it must have a public method called `execute`, which returns an instance of `ResponseInterface` (or its subclass),
* if the action has dependencies, they should be injected through the `execute()` method's signature, e.g.:

```php
use App\Http\Request\DummyRequest;
use App\Service\DummyServiceInterface;
use Psr\Http\Message\ResponseInterface;

public function execute(DummyRequest $request, DummyServiceInterface $dummyService): ResponseInterface
{
    // ...
}
```

**Although possible, it is not recommended injecting dependencies into an HTTP action through its constructor!**

## Requests

If the HTTP action needs to get some data from the incoming HTTP request, you may define a custom request class for said 
action. The request class should be created in the `src/Http/Request` directory and must extend the
`Noctis\KickStart\Http\Request\Request` class. 

You can find an example of a custom request class in the `src/Http/Request/DummyRequest.php` file.

The `Request` object offers the following methods:

* `getFiles()` - returns an array of uploaded files (instances of `Psr\Http\Message\UploadedFileInterface`, if there 
  were any.

## Responses

HTTP action's `execute()` method must return an instance of a class implementing the
`Psr\Http\Message\ResponseInterface\ResponseInterface` interface. The `Noctis\KickStart\Http\Action\AbstractAction` 
abstract class which every HTTP action class extends provides you with a couple of methods which produce such a 
`ResponseInterface` object.

### HTML Response

If you wish to return HTML as response, you should call the action's `render()` method. The method takes up to two 
arguments:

* the first argument is the name of the Twig template file, as it is in the `templates` directory, e.g. `dummy.html.twig`
* the second argument is the optional list of parameters which should be passed to said Twig template.

You can learn more about Twig templates from 
[Twig's Official Documentation](https://twig.symfony.com/doc/3.x/templates.html).

### Redirection Response

If you wish for the action to return an HTTP redirection, you should call the `redirect()` method. This method takes up
to two arguments:

* the first argument is the URL you wish the redirect to, e.g. `sign-in` will redirect the user to `/sign-in`. If you
  wish to redirect the user to a URL outside of your site, i.e. to a different domain, pass in the full URL, starting 
  with `http://` or `https://`,
* the second argument is an optional list of parameters which will be added to the given URL as its query string.

The `redirect()` method causes an `302 Found` response to be issued.

### Attachment Response

If you wish for your action to return an attachment, i.e. a file which the user's Web browser should attempt to download,
you should call the `sendAttachment()` method.

This method accepts one argument - an instance of the `Noctis\KickStart\Http\Response\Attachment\Attachment` class.

You can learn more about sending files from your HTTP action [here](cookbook/Sending_Files.md).

## Flash Messages

Sometimes you may wish to redirect the user to a different action and display a message on that action's page, but you 
want that message to only be displayed once, just after the redirection. This is where something called "Flash Message"
comes into play.

A flash message is saved in the user's session and will remain there until it is retrieved, at which point it is removed 
from the session (forgotten).

To store a flash message in the current session, call the `setFlashMessage()` method in your action:

```php
use Psr\Http\Message\ResponseInterface;

public function execute(): ResponseInterface
{
    //...
    $this->setFlashMessage('Information has been saved.');
    //...
}
```

To retrieve the flash message (erasing it from memory), call the `getFlashMessage()` method in your action:

```php
use Psr\Http\Message\ResponseInterface;

public function execute(): ResponseInterface
{
    //...
    $message = $this->getFlashMessage();
    //...
}
```

If you wish to retrieve the current flash message, but you want it to remain in the session for one more fetch,
call `getFlashMessage()` with the value `true`:

```php
use Psr\Http\Message\ResponseInterface;

public function execute(): ResponseInterface
{
    //...
    $message = $this->getFlashMessage(true);
    //...
}
```

Doing this will cause the flash message to be saved for one more future retrieval.

Currently, only a single message, represented as a string value, can be kept as a flash message.

## Middleware

If you wish for a piece of code to be executed before HTTP action's `execute()` method is called, for example to check 
if a user is actually logged in, you may do so by creating a middleware class and attaching it to the route definition.

The middlewares defined in the route definition are executed before the HTTP action is called, in the order they have
been defined in the route definition. A middleware **may** generate response object and return it. In such case, the HTTP
action will NOT be called, and the response generated by the middleware object will be returned to the browser.

If you wish to learn more about PHP middleware, you will find more information about it 
[here](https://phil.tech/2016/why-care-about-php-middleware/).

## Recipes

* [Adding a new HTTP Action](cookbook/New_Http_Action.md)
* [Custom HTTP Request](cookbook/Custom_Http_Request.md)
* [Removing HTTP Functionality](cookbook/Removing_Http_Functionality.md)
* [Sending Files in Response](cookbook/Sending_Files.md)
* [Registering a Custom Twig Function](cookbook/Custom_Twig_Function.md)
* [Registering a Twig Extension](cookbook/Registering_Twig_Extension.md)
* 