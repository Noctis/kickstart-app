# HTTP-based application

Files related to an HTTP application can be found in the `public`, `src/Http` and `templates` folders.

The `public` folder contains the `index.php` file, which acts as an 
["entry" point](https://martinfowler.com/eaaCatalog/frontController.html) for every incoming HTTP request. Should you
wish to change the list of used [service providers](Service_Providers.md) for your Web-based application, this is the 
file you should be modifying. 

The `src/Http` folder contains the "meat" of an HTTP application. Here you will find a couple of things:

* HTTP actions, located in the `src/Http/Action` folder,
* custom HTTP requests, located in the `src/Http/Request` folder,
* HTTP middleware, located in the `src/Http/Middleware/Guard` folder,
* HTTP routes list, in the `src/Http/Routing/routes.php` file.

The `templates` folder, in the root directory of your project, contains the template files, a.k.a. views.

Here's how it all ties together.

## The Gist

Once a browser sends an HTTP request, the WWW server will forward it to the `public/index.php` file. This file will call
Kickstart's request handler and pass it an object representing the request. The request handler will use Kickstart's
router to check which of the routes in the `src/Http/Routing/routes.php` file matches the requested path. If a 
matching route is found, the `process()` method of the HTTP action class, referenced in the matching route will be 
called. 

The action will generate an object representing the HTTP response. That object will be returned to the request handler,
which will emit it to the Web browser, that made the original request.

If there were any middleware declared in the route definition, those will be called, in order, prior to calling the 
action. A middleware may generate and return its own response object. In such case, the action class' `process()`
method will not be called.

## Routes

A list of routes can be found in the `src/Http/Routing/routes.php` file. It's a simple PHP file, which returns an array 
of `Noctis\KickStart\Http\Routing\Route` objects.

The `Route`'s constructor can take either 3 or 4 arguments:

* the HTTP method name - currently only `GET` and `POST` values are supported,
* the URI, e.g. `/`, `/foo` or `/product/show`,
* the class name of the HTTP action,

The 4th element is optional - it's an array of HTTP middleware, in the form of a list of middleware class names, all
implementing the `Psr\Http\Server\MiddlewareInterface` (see: [PSR-15](https://www.php-fig.org/psr/psr-15/))
interface.

If you do not wish for the route to utilize any middleware, you should simply omit that last constructor argument or 
declare it as an empty array (`[]`).

Here's an example of a route definition with no middleware:

```php
use App\Http\Action\DummyAction;
use App\Http\Middleware\Guard\DummyGuard;
use Noctis\KickStart\Http\Routing\Route;

// ...
return [
    new Route('GET', '/', DummyAction::class),
];
```

And here's an example of a route definition with one middleware:

```php
use App\Http\Action\DummyAction;
use App\Http\Middleware\Guard\DummyGuard;
use Noctis\KickStart\Http\Routing\Route;

// ...
return [
    new Route('GET', '/', DummyAction::class, [DummyGuard::class]),
];
```

## HTTP Actions

Every defined route should have an HTTP action class defined. These are like the ever popular Controllers, except where 
a Controller has multiple methods, each for different routes, an HTTP action only has one public method - `process()`.

There are a couple of requirements every HTTP action class must meet:

* it must implement the `Noctis\KickStart\Http\Action\ActionInterface` interface,
* it must be PSR-15 compliant, i.e. have a public method called `process`, with the following signature:
  ```php
  use Psr\Http\Message\ResponseInterface;
  use Psr\Http\Message\ServerRequestInterface;
  use Psr\Http\Server\RequestHandlerInterface;

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
  {
      // ...
  }
  ```
* if the action has dependencies, they should be injected through the action's constructor, e.g.:
  ```php
  use App\Service\DummyServiceInterface;

  private DummyServiceInterface $dummyService;

  public function __construct(DummyServiceInterface $dummyService)
  {
      $this->dummyService = $dummyService
  }
  
  // ...
  ```

## Requests

If the HTTP action needs to get some data from the incoming HTTP request, the following methods are available in the
`$request` object, in the action's `process()` method:

* `getQueryParams()` - returns an array of parameters passed in the query string; usually available when the request was 
  sent using HTTP's `GET` method (equivalent of PHP's `$_GET` super-global),
* `getParsedBody()` - returns an array of parameters passed in the request's body; available when the request was sent
  using the HTTP's `POST` method (equivalent of PHP's `$_POST` super-global),
* `getAttribute()` - returns an attribute of the given name, from the request. Request attributes can be set by:
  * Kickstart's router - [FastRoute](https://github.com/nikic/FastRoute), if named requested params were used in the
    route definition (see: `src/Http/Routing/routes.php` file),
  * HTTP middleware, e.g. [`middlewares/client-ip`](https://github.com/middlewares/client-ip).
* `getUploadedFiles()` - returns an array of uploaded files, i.e. instances of 
  `Psr\Http\Message\UploadedFileInterface` objects (sorta equivalent of PHP's `$_FILES` super-global).

If you wish to have your own, additional methods available in the request object, please refer to the ["Custom HTTP
Requests" article](Custom_Http_Requests.md).

### Named Route Parameters

Kickstart utilizes the [FastRoute](https://github.com/nikic/FastRoute) library for HTTP routing. FastRoute allows you
to define [named route parameters](https://github.com/nikic/FastRoute#defining-routes), as part of the route definition, 
for example:

```php
// src/Http/Routing/routes.php

use App\Http\Action\DummyAction;
use App\Http\Middleware\Guard\DummyGuard;
use Noctis\KickStart\Http\Routing\Route;

// ...
return [
    // ...
    new Route('GET', '/product/{productID:\d+}/details', DummyAction::class),
];
```

Now, when a matching URL is used, for example: `/product/13/details`, the value `13` will be available in the request
object, under the `productID` name. To retrieve it, call the `getAttribute()` method on the request object:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Noctis\KickStart\Http\Action\ActionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DummyAction implements ActionInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $productID = $request->getAttribute('productID');
    }
}
```

You could also take advantage of Kickstart's [custom HTTP requests](Custom_Http_Requests.md) functionality and create a
custom HTTP request class, with a dedicated method for fetching the route parameter, for example:

```php
<?php

declare(strict_types=1);

namespace App\Http\Request;

use Noctis\KickStart\Http\Request\AbstractRequest;

final class DummyRequest extends AbstractRequest
{
    public function getProductID(): int
    {
        return (int)$this->getAttribute('productID');
    }
}
```

Then, in your action you'd do something like this:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Http\Request\DummyRequest;
use Noctis\KickStart\Http\Action\ActionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DummyAction implements ActionInterface
{
    private DummyRequest $request;

    public function __construct(DummyRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $productID = $this->request
            ->getProductID();
    }
}
```

## Responses

HTTP action's `process()` method must return an instance of a class implementing the
`Psr\Http\Message\ResponseInterface` interface. Kickstart comes with a bunch of those, provided by the 
[`laminas/laminas-diactoros` package](https://docs.laminas.dev/laminas-diactoros/v2/custom-responses/):

* `Laminas\Diactoros\Response\HtmlResponse` - for HTML responses,
* `Laminas\Diactoros\Response\RedirectResponse` - for an HTTP redirection response,
* `Laminas\Diactoros\Response\TextResponse` - for a text response,
* `Laminas\Diactoros\Response\EmptyResponse` - for a response with no body (HTTP headers only),
* etc.

Plus, one additional response, for [sending attachments](cookbook/Sending_Attachments.md):
`Noctis\KickStart\Http\Response\AttachmentResponse`.

To create a response of your choice, you can use of the following response factories:

* `Noctis\KickStart\Http\Response\Factory\AttachmentResponseFactory` - for creating an `AttachmentResponse`,
* `Noctis\KickStart\Http\Response\Factory\HtmlResponseFactory` - for creating an `HtmlResponse`,
* `Noctis\KickStart\Http\Response\Factory\NotFoundResponseFactory` - for creating an 404 response, with (`TextResponse`) 
  or without (`EmptyResponse`) additional text message.
* `Noctis\KickStart\Http\Response\Factory\RedirectResponseFactory` - for creating a `RedirectResponse` response.

You can find examples on how to use it, below.

### HTML Response

If you wish to return HTML as response, you should call the `render()` method of the `HtmlResponseFactory`. This method 
takes up to two arguments:

* the first argument is the name of the Twig template file, as it is in the `templates` directory, e.g. 
  `dummy.html.twig`,
* the second argument is the optional list of parameters which should be passed to said Twig template.

You can learn more about Twig templates from 
[Twig's Official Documentation](https://twig.symfony.com/doc/3.x/templates.html).

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Laminas\Diactoros\Response\HtmlResponse;
use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Response\Factory\HtmlResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DummyAction implements ActionInterface
{
    private HtmlResponseFactoryInterface $htmlResponseFactory;

    public function __construct(HtmlResponseFactoryInterface $htmlResponseFactory)
    {
        $this->htmlResponseFactory = $htmlResponseFactory;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): HtmlResponse
    {
        return $this->htmlResponseFactory
            ->render('dummy.html.twig', [
                'foo' => 'bar',
            ]);
    }
}
```

### Redirection Response

If you wish for the action to return an HTTP redirection, you should call the `toPath()` method of the 
`RedirectResponseFactory`. This method takes up to two arguments:

* the first argument is the URL you wish the redirect to, e.g. `sign-in` will redirect the user to `/sign-in`. If you
  wish to redirect the user to a URL outside of your site, i.e. to a different domain, pass in the full URL, starting 
  with `http://` or `https://`,
* the second argument is an optional list of parameters which will be added to the given URL as its query string.

The `toPath()` method returns a `302 Found` redirection response.

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Laminas\Diactoros\Response\RedirectResponse;
use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Response\Factory\RedirectResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DummyAction implements ActionInterface
{
    private RedirectResponseFactoryInterface $redirectResponseFactory;

    public function __construct(RedirectResponseFactoryInterface $redirectResponseFactory)
    {
        $this->redirectResponseFactory = $redirectResponseFactory;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): RedirectResponse
    {
        return $this->redirectResponseFactory
            ->toPath('sign-in');
    }
}
```

### Attachment Response

If you wish for your action to return an attachment, i.e. a file which the user's Web browser should attempt to download,
you should call one of the methods available in the `AttachmentResponseFactory`.

You can learn more about sending attachments from your HTTP action [here](cookbook/Sending_Attachments.md).

## Flash Messages

Sometimes you may wish to redirect the user to a different action and display a message on that action's page, but you 
want that message to only be displayed once, just after the redirection. This is where something called "Flash Message"
comes into play.

A flash message is saved in the current session and will remain there until it is retrieved, at which point it will be
removed from the session (forgotten).

To store a flash message in the current session, use the `setFlashMessage()` method of the 
`Noctis\KickStart\Http\Service\FlashMessageService` class, in your action:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Laminas\Diactoros\Response\RedirectResponse;
use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Response\Factory\RedirectResponseFactoryInterface;
use Noctis\KickStart\Http\Service\FlashMessageServiceInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class FooAction implements ActionInterface
{
    private FlashMessageServiceInterface $flashMessageService;
    private RedirectResponseFactoryInterface $redirectResponseFactory;

    public function __construct(
        FlashMessageServiceInterface $flashMessageService,
        RedirectResponseFactoryInterface $redirectResponseFactory
    ) {
        $this->flashMessageService = $flashMessageService;
        $this->redirectResponseFactory = $redirectResponseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): RedirectResponse
    {
        $this->flashMessageService
            ->setFlashMessage('info', 'Information has been saved.');

        return $this->redirectResponseFactory
            ->toPath('bar');
    }
}
```

To retrieve the flash message (erasing it from memory), call the `getFlashMessage()` method of the
`Noctis\KickStart\Http\Service\FlashMessageService` class, in your action:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Laminas\Diactoros\Response\HtmlResponse;
use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Response\Factory\HtmlResponseFactoryInterface;
use Noctis\KickStart\Http\Service\FlashMessageServiceInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class BarAction implements ActionInterface
{
    private FlashMessageServiceInterface $flashMessageService;
    private HtmlResponseFactoryInterface $htmlResponseFactory;

    public function __construct(
        FlashMessageServiceInterface $flashMessageService,
        HtmlResponseFactoryInterface $htmlResponseFactory
    ) {
        $this->flashMessageService = $flashMessageService;
        $this->htmlResponseFactory = $htmlResponseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): HtmlResponse
    {
        $message = $this->flashMessageService
            ->getFlashMessage('info');

        return $this->htmlResponseFactory
            ->render('bar.html.twig', [
                'message' => $message
            ]);
    }
}
```

If you wish to retrieve the current flash message, but you want it to remain in the session for one more fetch,
call `getFlashMessage()` with the value `true` as its second argument. Doing this will cause the flash message to be 
saved for one more future retrieval.

## Middleware

If you wish for a piece of code to be executed before HTTP action's `process()` method is called, for example to check 
if a user is actually logged in, you may do so by creating a middleware class and attaching it to the route definition.

The middlewares defined in the route definition are executed before the HTTP action's `process()` method is called, in 
the order they have been defined in the route definition. A middleware **may** generate response object and return it. 
In such case, the action's `process()` method will NOT be called, and the response generated by the middleware object 
will be returned to the Web browser.

If you wish to learn more about PHP middleware, you will find more information about it 
[here](https://phil.tech/2016/why-care-about-php-middleware/).

## Recipes

* [Adding a new HTTP Action](cookbook/New_Http_Action.md)
* [Removing HTTP Functionality](cookbook/Removing_Http_Functionality.md)
* [Sending Attachments in Response](cookbook/Sending_Attachments.md)
* [Registering a Custom Twig Function](cookbook/Custom_Twig_Function.md)
* [Registering a Twig Extension](cookbook/Registering_Twig_Extension.md)
