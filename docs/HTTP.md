# HTTP-based application

Files related to an HTTP application can be found in the `config`, `public`, `src/Http` and `templates` folders.

The `config` folder contains the `routes.php` file, which contains the list of routes, i.e. mappings between URLs and
HTTP actions available in the application.

The `public` folder contains the `index.php` file, which acts as an 
["entry" point](https://martinfowler.com/eaaCatalog/frontController.html) for every incoming HTTP request. Should you
wish to change the list of used [service providers](Service_Providers.md) for your Web-based application, this is the 
file you should be modifying. 

The `src/Http` folder contains the "meat" of an HTTP application. Here you will find a couple of things:

* HTTP actions, located in the `src/Http/Action` folder,
* custom HTTP requests, located in the `src/Http/Request` folder,
* HTTP middleware, located in the `src/Http/Middleware/Guard` folder.

The `templates` folder, in the root directory of your project, contains the template files, a.k.a. views.

Here's how it all ties together.

## The Gist

Once a browser sends an HTTP request, the WWW server will forward it to the `public/index.php` file. This file will call
Kickstart's request handler and pass it an object representing the request. The request handler will use Kickstart's
router to check which of the routes in the `config/routes.php` file matches the requested path. If a matching route is
found, the `process()` method of the HTTP action class, referenced in the matching route will be called. 

The action will generate an object representing the HTTP response. That object will be returned to the request handler,
which will emit it to the Web browser, that made the original request.

If there were any middleware declared in the route definition, those will be called, in order, prior to calling the 
action. A middleware may generate and return its own response object. In such case, any further middlewares & the action
itself will not be called.

## Routing

You can read all about how Kickstart does HTTP routing [here](Routing.md).

## Requests

If the HTTP action needs to get some data from the incoming HTTP request, the following methods are available in the
`$request` object, in the action's `process()` method:

* `getQueryParams()` - returns an array of parameters passed in the query string; usually available when the request was 
  sent using HTTP's `GET` method (equivalent of PHP's `$_GET` super-global),
* `getParsedBody()` - returns an array of parameters passed in the request's body; available when the request was sent
  using the HTTP's `POST` method (equivalent of PHP's `$_POST` super-global),
* `getAttribute()` - returns an attribute of the given name, from the request. Request attributes can be set by:
  * Kickstart's router - [FastRoute](https://github.com/nikic/FastRoute), if named requested params were used in the
    route definition (see: [`config/routes.php`](../config/routes.php) file),
  * HTTP middleware, e.g. [`middlewares/client-ip`](https://github.com/middlewares/client-ip).
* `getUploadedFiles()` - returns an array of uploaded files, i.e. instances of 
  `Psr\Http\Message\UploadedFileInterface` objects (sorta equivalent of PHP's `$_FILES` super-global).

If you wish to have your own, additional methods available in the request object, please refer to the ["Custom HTTP
Requests" article](Custom_Http_Requests.md).

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

To generate the appropriate response object you can use the following traits, available in the 
`Noctis\KickStart\Http\Helper` namespace:

* `RenderTrait` - to generate a `HtmlResponse`,
* `RedirectTrait` - to generate a `RedirectResponse`,
* `AttachmentTrait` - to generate a `AttachmentResponse`, or
* `NotFoundTrait` - to generate a 404 response, in the form of a `EmptyResponse` or a `TextResponse`.

### HTML Response

To create an HTML response object, include the `Noctis\KickStart\Http\Helper\RenderTrait` trait into your action and
make sure an instance of the `Noctis\KickStart\Http\Service\RenderService` is injected into the
local `$renderService` field:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Helper\RenderTrait;
use Noctis\KickStart\Http\Service\RenderServiceInterface;

final class DummyAction implements ActionInterface
{
    use RenderTrait;

    public function __construct(RenderServiceInterface $renderService)
    {
        $this->renderService = $renderService;
    }

    // ...
}
```

You can then use the trait's `render()` method to create an instance of `Laminas\Diactoros\Response\HtmlResponse`. The
entire action will then look like this:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Laminas\Diactoros\Response\HtmlResponse;
use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Helper\RenderTrait;
use Noctis\KickStart\Http\Service\RenderServiceInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DummyAction implements ActionInterface
{
    use RenderTrait;

    public function __construct(RenderServiceInterface $renderService)
    {
        $this->renderService = $renderService;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): HtmlResponse
    {
        return $this->render('dummy.html.twig', [
            'foo' => 'bar',
        ]);
    }
}
```

The `render()` method takes up to two arguments:

* the first argument is the name of the Twig template file, as it is in the `templates` directory, e.g. 
  `dummy.html.twig`,
* the second argument is the optional list of parameters which should be passed to said Twig template.

You can learn more about Twig templates from 
[Twig's Official Documentation](https://twig.symfony.com/doc/3.x/templates.html).

### Redirection Response

To create an HTML redirection object, include the `Noctis\KickStart\Http\Helper\RedirectTrait` trait into your action
and make sure an instance of the `Noctis\KickStart\Http\Service\RedirectService` is injected into its 
`$renderService` field:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Helper\RedirectTrait;
use Noctis\KickStart\Http\Service\RedirectServiceInterface;

final class DummyAction implements ActionInterface
{
    use RedirectTrait;

    public function __construct(RedirectServiceInterface $redirectService)
    {
        $this->redirectService = $redirectService;
    }

    // ...
}
```

You can then use the trait's `redirect()` or `redirectToRoute()` method to create an instance of 
`Laminas\Diactoros\Response\RedirectResponse`. The entire action will then look like this:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Laminas\Diactoros\Response\RedirectResponse;
use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Helper\RedirectTrait;
use Noctis\KickStart\Http\Service\RedirectServiceInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DummyAction implements ActionInterface
{
    use RedirectTrait;

    public function __construct(RedirectServiceInterface $redirectService) 
    {
        $this->redirectService = $redirectService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): RedirectResponse
    {
        return $this->redirect('sign-in');
    }
}
```

The `redirect()` method takes up to two parameters:

* the first parameters is the URL you wish the redirect to, e.g. `sign-in` will redirect the user to `/sign-in`. If you 
  wish to redirect the user to a URL outside of your site, i.e. to a different domain, pass in the full URL, starting
  with `http://` or `https://`,
* the second argument is an optional list of parameters which will be added to the given URL as its query string.

The `redirectToRoute()` method also takes up to two parameters:

* the first parameter is the name of the route (see: [Named Routes](Routing.md#named-routes) section in the 
  [Routing](Routing.md) article),
* the second parameter is an array of parameters; those will firstly be used to replace any named parameters in the
  route's path (if there are any), and any that are left will be used to build the query string of the URL.

Both the `redirect()` and `redirectToRoute()` methods return a `302 Found` HTTP response, with no body.

### Attachment Response

If you wish for your action to return an attachment, i.e. a file which the user's Web browser should attempt to 
download, you should call one of the methods provided by the `Noctis\KickStart\Http\Helper\AttachmentTrait`.

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
use Noctis\KickStart\Http\Helper\RedirectTrait;
use Noctis\KickStart\Http\Service\FlashMessageServiceInterface;
use Noctis\KickStart\Http\Service\RedirectServiceInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class FooAction implements ActionInterface
{
    use RedirectTrait;

    private FlashMessageServiceInterface $flashMessageService;

    public function __construct(
        FlashMessageServiceInterface $flashMessageService,
        RedirectServiceInterface $redirectService
    ) {
        $this->flashMessageService = $flashMessageService;
        $this->redirectService = $redirectService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): RedirectResponse
    {
        $this->flashMessageService
            ->setFlashMessage('info', 'Information has been saved.');

        return $this->redirect('bar');
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
use Noctis\KickStart\Http\Helper\RenderTrait;
use Noctis\KickStart\Http\Service\FlashMessageServiceInterface;
use Noctis\KickStart\Http\Service\RenderServiceInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class BarAction implements ActionInterface
{
    use RenderTrait;

    private FlashMessageServiceInterface $flashMessageService;

    public function __construct(
        FlashMessageServiceInterface $flashMessageService,
        RenderServiceInterface $renderService
    ) {
        $this->flashMessageService = $flashMessageService;
        $this->renderService = $renderService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): HtmlResponse
    {
        $message = $this->flashMessageService
            ->getFlashMessage('info');

        return $this->render('bar.html.twig', [
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
