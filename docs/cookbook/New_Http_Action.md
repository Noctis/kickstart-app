# Creating a new HTTP Action

To add a new HTTP action, you need to do two things:

* create an HTTP action class,
* add a route definition, referencing the action class.

Optionally, you can also:

* create a template (view) for said action,
* create a custom HTTP request class for the action, if the action takes any request parameters.

## Creating an HTTP action class

All HTTP action classes:

* reside in the `src/Http/Action` folder,
* extend the `Noctis\KickStart\Http\Action\AbstractAction` abstract class,
* contain a method named `execute` which returns an implementation of `Psr\Http\Message\ResponseInterface` interface.

Let's start by creating a file named `FormAction` in the `src/Http/Action` folder:

```php
<?php

namespace App\Http\Action;

use Noctis\KickStart\Http\Action\AbstractAction;
use Psr\Http\Message\ResponseInterface;

final class EditAction extends AbstractAction
{
    public function execute(): ResponseInterface
    {
    }
}
```

This HTTP action class is almost complete. The only thing it's missing is the definition of the `execute()` method, but
we'll get back to that later. For now, lets create a route definition and reference our action class in it:

## Adding a Route Definition With HTTP Action Reference

By default, all HTTP route definitions can be found in the `src/Http/Routing/routes.php` file:

```php
<?php

declare(strict_types=1);

use App\Http\Action\DummyAction;
use App\Http\Middleware\Guard\DummyGuard;

return [
    ['GET', '/', DummyAction::class, [DummyGuard::class]],
];
```

Kickstart uses the [FastRoute](https://github.com/nikic/FastRoute) package for defining routes and for routing itself.

Let's say we want our action (`FormAction`) to be executed when someone visits the `/form` URL in our website. Add the
following line to the array within the `routes.php` file:

```php
<?php

declare(strict_types=1);

use App\Http\Action\DummyAction;
use App\Http\Action\FormAction;
use App\Http\Middleware\Guard\DummyGuard;

return [
    ['GET', '/', DummyAction::class, [DummyGuard::class]],
    ['GET', '/form', FormAction::class],
];
```

OK, now whenever someone tries to visit the `/form` URL of your website, the `FormAction::execute()` method will be 
called. But... remember that the action's `execute()` method still lacks its definition, i.e. it's empty. Let's fix that.

## Creating a Template (view) For An HTTP Action

An HTTP action's `execute()` method must always return an object implementing the  `Psr\Http\Message\ResponseInterface` 
interface, so we'll need to create it somehow. The `AbstractAction` class which `FormAction` class extends has a 
`render()` method which does just that - creates a `Laminas\Diactoros\Response\HtmlResponse` object.

What the `render()` method does is, it takes the given template (view) file name, generates HTML from it and wraps it in 
a `HtmlResponse` object. Let's create a simple template file, make `FormAction::execute()` method render it and return 
the generated response.

First, create an empty file named `form.html.twig` in the `templates` folder. Next, put some HTML in it:

```twig
{% extends "layout.html.twig" %}

{% block content %}
    <div class="container">
        <!-- HTML goes here -->
    </div>
{% endblock %}
```

Next, let's finish implementing the `execute()` method:

```php
use Psr\Http\Message\ResponseInterface;

public function execute(): ResponseInterface
{
    return $this->render('form.html.twig');
}
```

The route is defined (`/form`), pointing to `FormAction::execute()` method, which renders the `templates/form.html.twig`
file and returns the generated HTML.

Here's how the `FormAction` class should look like, in full:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Noctis\KickStart\Http\Action\AbstractAction;
use Psr\Http\Message\ResponseInterface;

final class FormAction extends AbstractAction
{
    public function execute(): ResponseInterface
    {
        return $this->render('form.html.twig');
    }
}
```
