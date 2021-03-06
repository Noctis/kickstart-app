# Creating a New HTTP Action

To add a new HTTP action, you need to do two things:

* create an HTTP action class,
* add a route definition, referencing the action class.

Optionally, you can also:

* create a template (view) for said action,
* create a [custom HTTP request class](../Custom_Http_Requests.md) for the action, if the action takes any request 
  parameters.

## Creating an HTTP Action Class

All HTTP action classes:

* reside in the `src/Http/Action` folder,
* implement the `Noctis\KickStart\Http\Action\ActionInterface` interface,
* contain a method named `process()` which returns an object of a class implementing the 
  `Psr\Http\Message\ResponseInterface` interface.

Let's start by creating a class named `FormAction` in the `src/Http/Action` folder:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Noctis\KickStart\Http\Action\ActionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class FormAction implements ActionInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
    }
}
```

This HTTP action class is almost complete. The only thing that's missing is the definition of the `process()` method, 
but we'll get back to that later. For now, lets create a route definition and reference our action class in it:

## Adding a Route Definition

By default, all HTTP route definitions can be found in the `src/Http/Routing/routes.php` file:

```php
<?php

declare(strict_types=1);

use App\Http\Action\DummyAction;
use App\Http\Middleware\Guard\DummyGuard;
use Noctis\KickStart\Http\Routing\Route;

return [
    Route::get('/', DummyAction::class, [DummyGuard::class]),
];
```

Kickstart uses the [FastRoute](https://github.com/nikic/FastRoute) package for defining routes and for routing itself.

Let's say we want our action (`FormAction`) to be executed when someone visits the `/form` URL in our website. Add the
following line to the array within the `routes.php` file:

```php
<?php

declare(strict_types=1);

use App\Http\Action\FormAction;
use Noctis\KickStart\Http\Routing\Route;

return [
    // ...
    Route::get('/form', FormAction::class),
];
```

OK, now whenever someone tries to visit the `/form` URL of your website, the `FormAction::process()` method will be 
called. But... remember that the action's `process()` method still lacks its definition, i.e. it's empty. Let's fix 
that.

## Creating a Template (View) For an HTTP Action

An HTTP action's `process()` method must always return an object implementing the `Psr\Http\Message\ResponseInterface` 
interface, so we'll need to create it somehow. The `Noctis\KickStart\Http\Helper\RenderTrait` trait offers a `render()` 
method which does just that - creates a `Laminas\Diactoros\Response\HtmlResponse` object, which implements the 
aforementioned `ResponseInterface` interface.

What the `render()` method does is, it takes the given template (view) file, generates HTML from it and wraps it in 
a `HtmlResponse` object. Let's create a simple template file, make `FormAction::process()` method render it and return 
the generated response object.

Create an empty file named `form.html.twig` in the `templates` folder. Next, put some HTML in it:

```twig
{% extends "layout.html.twig" %}

{% block content %}
    <div class="container">
        <!-- HTML goes here -->
    </div>
{% endblock %}
```

Next, let's finish implementing the `process()` method. First, let's include the `RenderTrait` and provide it with an
instance of `HtmlResponseFactory` in the `$htmlResponseFactory` field:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Helper\RenderTrait;
use Noctis\KickStart\Http\Response\Factory\HtmlResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class FormAction implements ActionInterface
{
    use RenderTrait;
    
    public function __construct(HtmlResponseFactoryInterface $htmlResponseFactory)
    {
        $this->htmlResponseFactory = $htmlResponseFactory;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
    }
}
```

Now lets put some "meat" on the `process()` method's skeleton:

```php
/**
 * @inheritDoc
 */
public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
{
    return $this->render('form.html.twig');
}
```

The route is defined (`/form`), pointing to `FormAction` class, whose `process()` method renders the 
`templates/form.html.twig` file and returns the generated HTML.

Here's how the `FormAction` class should look like, in full:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Helper\RenderTrait;
use Noctis\KickStart\Http\Response\Factory\HtmlResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class FormAction implements ActionInterface
{
    use RenderTrait;

    public function __construct(HtmlResponseFactoryInterface $htmlResponseFactory)
    {
        $this->htmlResponseFactory = $htmlResponseFactory;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->render('form.html.twig');
    }
}
```

The route is defined (`/form`), pointing to `FormAction::process()` method, which renders the `templates/form.html.twig`
file and returns the generated HTML.

If you want to be thorough, modify the `process()` method's signature, to indicate that it returns an instance of 
`Laminas\Diactoros\Response\HtmlResponse`:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Laminas\Diactoros\Response\HtmlResponse;
use Noctis\KickStart\Http\Action\ActionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class FormAction implements ActionInterface
{
    // ...

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): HtmlResponse
    {
        $this->render('form.html.twig');
    }
}
```
