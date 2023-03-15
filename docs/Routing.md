# Routing

Every Kickstart application has a collection of routes, each of which map URLs to specific HTTP actions. Said collection 
can be found in the `config/routes.php` file. The file contains an PHP array, where each element is an
`Noctis\KickStart\Http\Routing\Route` object. 

Each `Route` object specifies:

* the HTTP method (`GET`, `POST`, etc.),
* the URL (path), e.g. `/foo` and
* the corresponding action which should be executed if a request matches this specific path and method.

A route object may also contain a few additional pieces of information, all of which are optional:

* a list of [PSR-7](https://www.php-fig.org/psr/psr-7/)-compliant HTTP middleware, through which the request object 
  should be passed through, before being handed over to the HTTP action, and
* the custom request class which should be used, instead of Kickstart's standard one.

The `Route` class offers a few static factory methods, which define the HTTP method for a given route. Those are:

* `get()`,
* `post()`,
* `put()`,
* `patch()`,
* `delete()`.

I don't think I have to explain to which HTTP request methods these factory methods correspond to :)

## Route Parameters

Let's go over each of the `Route`'s methods' parameters, in more detail.

### First Parameter: Path

Given path should always start with a forward slash: `/`, e.g.: 

```php
Route::get('/', \App\Http\Action\DummyAction::::class);
```

```php
Route::get('/foo', \App\Http\Action\DummyAction::::class);
```

```php
Route::get('/foo/bar', \App\Http\Action\DummyAction::::class);
```

The first route in the collection which matches the request's HTTP method and path will be used.

#### Named Parameters in Path

A given path may contain **named parameters**, e.g.:

```php
Route::get('/document/{id}/show', \App\Http\Action\ShowDocumentAction::class),
```

This route will match any of the following requests:

```
GET /document/1/show
```

```
GET /document/13/show
```

```
GET /document/foo/show
```

In the above example, you can the find the value the `id` path parameter in the request object, by calling the 
`getAttribute()` method, like so:

```php
$request->getAttribute('id');
```

It is possible to specify additional requirements for a given named path parameter for it to match the given route. For 
example if you only want the following request:

```
GET /document/13/show
```

to match your:

```php
Route::get('/document/{id}/show', \App\Http\Action\ShowDocumentAction::class),
```

path, define the route as such:

```php
Route::get('/document/{id:\d+}/show', \App\Http\Action\ShowDocumentAction::class),
```

The part after the semicolon (`:`) in the path definition - the `\d+` thing - is a standard 
[regular expression](https://en.wikipedia.org/wiki/Regular_expression).

It is also possible to define a specific path parameter as optional. For that, just wrap it in square brackets 
(`[]`), for example:

```php
Route::get('/project[/{title}]', \App\Http\Action\ShowDocumentAction::class),
```

Both of the following requests will match the route above:

```
GET /project
```

```
GET /project/foo
```

**IMPORTANT:** Named route parameters' values, returned by the `getAttribute()` method, are always
[URL-encoded](http://www.faqs.org/rfcs/rfc3986.html). For example, in case of the following route:

```php
Route::get('/document/{name}/show', \App\Http\Action\ShowDocumentAction::class),
```

when the following URL is called:

```
GET /document/foo bar/show
```

the following will happen, when you call `getAttribute()`:

```php
$name = $request->getAttribute('name');     // will return "foo%20name"
```

In such case, you should use PHP's either [`urldecode()`](https://www.php.net/urldecode) or
[`rawurldecode()`](https://www.php.net/manual/en/function.rawurldecode.php) functions, for example:

```php
$name = urldecode(      // will return "foo name"
    $request->getAttribute('name')
);
```

### Second Parameter: Action Name

To specify an action in a route definition, you should reference it by its full class name, e.g.:

```php
Route::get('/', \App\Http\Action\DummyAction::class);
```

**IMPORTANT:** Each action class must meet **all** of the following requirements:

* must implement the `Noctis\KickStart\Http\Action\ActionInterface` interface, and
* must be [PSR-15 compliant](https://www.php-fig.org/psr/psr-15/#22-psrhttpservermiddlewareinterface), i.e. have a 
  public method called `process`, with the following signature:
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
          // ...
      }
  }
  ```

If the action has dependencies, they should be injected through the action's constructor, e.g.:
  ```php
  <?php

  declare(strict_types=1);
  
  namespace App\Http\Action;
  
  use App\Service\DummyServiceInterface;
  use Noctis\KickStart\Http\Action\ActionInterface;
  use Psr\Http\Message\ResponseInterface;
  use Psr\Http\Message\ServerRequestInterface;
  use Psr\Http\Server\RequestHandlerInterface;
  
  final class DummyAction implements ActionInterface
  {
      public function __construct(
          private readonly DummyServiceInterface $dummyService
      ) {      
      }
  
      /**
       * @inheritDoc
       */
      public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
      {
          // ...
      }
  }
  ```

### Third Parameter: List of Middlewares (optional)

The middlewares list should be provided as a list of class names, all of which must implement 
[PSR-15's](https://www.php-fig.org/psr/psr-15/#22-psrhttpservermiddlewareinterface) 
`Psr\Http\Server\MiddlewareInterface`, e.g:

```php
Route::get('/', \App\Http\Action\DummyAction::class, , [\App\Http\Middleware\Guard\DummyMiddleware::class]);
```

Kickstart will create an object representing the HTTP request received (with everything that comes with it) and then
pass it to each of the middlewares on the list, in the order there were given, from the first one onward.

After the request object passes through the last middleware on the list, it will be passed on to the action. The action
is always the last one to get the request object.

**IMPORTANT:** Please remember that:

* each middleware has the right to modify the request object, before passing it on to the next middleware in chain,
* each middleware has the right to generate and return an object representing an HTTP response; if that happens, all the
  following middlewares in the chain and the HTTP action itself **will NOT be called**, they will not receive the 
  request object.

### Fourth Parameter: Custom Request Class

You can read all about the custom request classes [here](Custom_Http_Requests.md).

If no value is provided for this argument, the given HTTP action will receive an instance of Kickstart's standard
request class - `Noctis\KickStart\Http\Request\Request`.

## Named Routes

Each route in the routes list can have a name, e.g.:

```php
<?php

declare(strict_types=1);

use App\Http\Action\DummyAction;
use App\Http\Action\SignInAction;
use Noctis\KickStart\Http\Routing\Route;

return [
    'home' => Route::get('/', DummyAction::class),
    'sign-in-form' => Route::get('/sign-in', SignInAction::class),
    // ...
];
```

Not every route needs to have a name, but every name must be unique, i.e. no two routes may have the same name. 

Route names are optional, i.e. a route will work perfectly fine without a name specified, it's just that you won't be 
able to refer to that route by its name.

Named routes can be referenced in Twig templates and in your PHP code.

### Referencing Named Routes in Twig Templates

You can refer to a route by its name in a Twig template by using the `route()` function, eg.:

```html
{% extends "layout.html.twig" %}

{% block content %}
    <div class="container">
        <a href="{{ route('sign-in-form') }}" class="btn btn-small btn-primary">Sign-In</a>
    </div>
{% endblock %}
```

This will result in the following HTML being generated:

```html
<div class="container">
    <a href="/sign-in" class="btn btn-small btn-primary">Sign-In</a>
</div>
```

If your route uses named parameters, eg.:

```php
<?php

declare(strict_types=1);

use App\Http\Action\ShowDocumentAction;
use Noctis\KickStart\Http\Routing\Route;

return [
    'show-document' => Route::get('/document/{id}/show', ShowDocumentAction::class),
    // ...
];
```

you can provide their values by passing an array as the second argument of the `route()` function, like so:

```html
{% extends "layout.html.twig" %}

{% block content %}
    <div class="container">
        <a href="{{ route('show-document', { id: '1' }) }}" class="btn btn-small btn-primary">Show</a>
    </div>
{% endblock %}
```

or, if you want to use a variable which is available in the view:

```html
{% extends "layout.html.twig" %}

{% block content %}
    <div class="container">
        <a href="{{ route('show-document', { id: document.id }) }}" class="btn btn-small btn-primary">Show</a>
    </div>
{% endblock %}
```

Any values provided in that array which do not match any of the route's named parameters, will be appended to the 
generated path as a query string, for example:

```html
{% extends "layout.html.twig" %}

{% block content %}
    <div class="container">
        <a href="{{ route('show-document', { id: '1', foo: 'bar' }) }}" class="btn btn-small btn-primary">Show</a>
    </div>
{% endblock %}
```

will result in the following HTML being generated:

```html
<div class="container">
    <a href="/document/1/show?foo=bar" class="btn btn-small btn-primary">Show</a>
</div>
```

### Referencing Named Routes in Your Code

Named routes can also be referenced outside Twig templates, in PHP code, in HTTP actions for example, when you need to
redirect the user. It's a little less straightforward than it is in Twig templates though.

For example, given the following routes list:

```php
<?php

declare(strict_types=1);

use App\Http\Action\DummyAction;
use App\Http\Action\RedirectingAction;
use App\Http\Action\WelcomeAction;
use Noctis\KickStart\Http\Routing\Route;

return [
    'home'         => Route::get('/', DummyAction::class),
    'redir'        => Route::get('/redirect', RedirectingAction::class),
    'welcome_page' => Route::get('/welcome', WelcomeAction::class)
];
```

If you want the `RedirectingAction` to redirect the user to the `welcome_page` route, you should:

* use the `Noctis\KickStart\Http\Helper\RedirectTrait` in the `RedirectingAction`, to get access to the 
  `redirectToRoute()` method, and
* provide the `RedirectTrait` with an instance of `Noctis\KickStart\Http\Service\RedirectService`.

Once you do that, you can use the `redirectToRoute()` method provided by the trait, like so:

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

final class RedirectingAction implements ActionInterface
{
    use RedirectTrait;

    public function __construct(RedirectServiceInterface $redirectService) 
    {
        $this->redirectService = $redirectService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): RedirectResponse
    {
        return $this->redirectToRoute('home');
    }
}
```

If you need to generate a path based on a specific named route elsewhere, i.e. outside of an HTTP action class, inject
an instance of `Noctis\KickStart\Service\PathGenerator` into your class, for example:

```php
<?php

declare(strict_types=1);

namespace App\Service;

use Noctis\KickStart\Service\PathGeneratorInterface;

final class DummyService implements DummyServiceInterface
{
    public function __construct(
        private readonly PathGeneratorInterface $pathGenerator
    ) {
    }
    
    // ...
}
```

and then you can use the `PathGenerator::toRoute()` method, for example:

```php
$path = $this->pathGenerator
    ->toRoute('welcome');
```

**IMPORTANT:** please note that this method returns an `Noctis\KickStart\ValueObject\GeneratedUriInterface` object, not
a string. If you need the path as a string, call the `toString()` method on the returned object:

```php
$path = $this->pathGenerator
    ->toRoute('welcome')
    ->toString();
```

**IMPORTANT:** if you provide an invalid route name, due to a typo for example, a 
`\Noctis\KickStart\Exception\RouteNotFoundException` exception will be thrown.
