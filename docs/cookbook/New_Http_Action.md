# Adding a new HTTP action

To add a new HTTP action, one needs to do two things:

* create an HTTP action class,
* assign said action to a route.

Optionally, one can also:

* create a template (view) for said action,
* create an HTTP request class for the action, if the action takes any request parameters.

## Creating an HTTP action class

All HTTP action classes:

* reside in the `src/Http/Action` folder,
* extend the `Noctis\KickStart\Http\Action\AbstractAction` abstract class,
* contain a method named `execute` which returns a `Response` type object.

Let's start by creating a file named `DummyAction` in the `src/Http/Action` folder:

```php
<?php

namespace App\Http\Action;

use Noctis\KickStart\Http\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Response;

final class DummyAction extends AbstractAction
{
    public function execute(): Response
    {
    }
}
```

This HTTP action class is almost complete. The only thing it's missing is the definition of the `execute()` method, but
we'll get back to that later. For now, lets define a route and assign our action to it.

## Assigning an HTTP action to a route

By default, all HTTP routes are defined in the `src/Http/Routes/StandardRoutes.php` file, or to be more specific, in the
`StandardRoutes::get()` method inside it:

```php
public function get(): callable
{
    return function (RouteCollector $r): void {
        $baseHref = $this->configuration
            ->getBaseHref();

        $r->addGroup(
            $baseHref,
            function (RouteCollector $r) {
            }
        );
    };
}
```

Kickstart uses the [`nikic/fast-route`](https://github.com/nikic/FastRoute) package for defining routes and routing itself.

Let's say we want our action (`DummyAction`) to be executed when someone visits the root page of our website (`/`).
Here's what should you add to `StandardRoutes::get()` method and where:

```php
public function get(): callable
{
    return function (RouteCollector $r): void {
        $baseHref = $this->configuration
            ->getBaseHref();

        $r->addGroup(
            $baseHref,
            function (RouteCollector $r) {
                $r->get('/', DummyAction::class);
            }
        );
    };
}
```

If your IDE does not do it by default, remember to import the `DummyAction` class at the top of the 
`StandardRoutes.php` file:

```php
<?php

declare(strict_types=1);

namespace App\Http\Routes;

use App\Configuration\FancyConfigurationInterface;
use App\Http\Action\DummyAction;
use FastRoute\RouteCollector;
use Noctis\KickStart\Http\Routing\HttpRoutesProviderInterface;
```

OK, so now whenever someone tries to visits the root (`/`) of your website, the `DummyAction::execute()` method will
be called. But, remember the `execute()` method still lacks its definition, i.e. it's empty. Let's fix that.

## Creating a template (view) for an HTTP action

An HTTP action's `execute()` method must always return a `Response` object, so we'll need to create it somehow. The
`AbstractHttpAction` class which `DummyAction` class extends defines a `render()` method which does just that - creates
a `Response` object.

What the `render()` method does, it takes the given template (view) file name, generates HTML from it and wraps it in a
`Response` object. Let's create a simple template file, make `DummyAction::execute()` method render it and return the
generated response.

First, create an empty file named `dummy.html.twig` in the `templates` folder. Next, put some HTML in it:

```twig
{% extends "layout.html.twig" %}

{% block content %}
    <div class="container">
        Hello, Guest. What is foo?
    </div>
{% endblock %}
```

Next, let's finish implementing the `execute()` method:

```php
public function execute(DummyRequest $request): Response
{
    return $this->render('dummy.html.twig');
}
```

If everything has been done correctly, you should see "_Hello, Guest. What is foo?_" when you visit the root (`/`) of
your website.

The route is defined (`/`), pointing to `DummyAction::execute()` method, which renders the `templates/dummy.html.twig`
file and returns the generated HTML.

Here's how the `DummyAction` class should look like, in full:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Http\Request\DummyRequest;
use Noctis\KickStart\Http\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Response;

final class DummyAction extends AbstractAction
{
    public function execute(DummyRequest $request): Response
    {
        return $this->render('dummy.html.twig');
    }
}
```
