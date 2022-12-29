# Custom HTTP Requests

If you wish to add your own, additional methods to a request object, you can do so by creating a custom HTTP request
class, which acts as decorator around the original request. Your custom request class must extend the
`Noctis\KickStart\Http\Request\Request` class and implement [PSR-7's](https://www.php-fig.org/psr/psr-7/) 
`Psr\Http\Message\ServerRequestInterface` interface, for example:

```php
<?php

declare(strict_types=1);

namespace App\Http\Request;

use Noctis\KickStart\Http\Request\Request;
use Psr\Http\Message\ServerRequestInterface;

final class DummyRequest extends Request implements ServerRequestInterface
{
    public function getFoo(): string
    {
        return 'foo';
    }
}
```

You must then reference your custom request class name in the route definition for your action. Routes list can be 
found in your application's `config/routes.php` file. The class name must be provided as the route's 4th argument, 
for example:

```php
<?php

declare(strict_types=1);

use App\Http\Action\DummyAction;
use App\Http\Middleware\Guard\DummyGuard;
use App\Http\Request\DummyRequest;
use Noctis\KickStart\Http\Routing\Route;

return [
    Route::get('/', DummyAction::class, [DummyGuard::class], DummyRequest::class),
];
```

An instance of your custom request class will be provided as the `$request` argument of the HTTP action's class:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Http\Request\DummyRequest;
use Laminas\Diactoros\Response\HtmlResponse;
use Noctis\KickStart\Http\Action\ActionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DummyAction implements ActionInterface
{
    // ...

    /**
     * @param DummyRequest $request
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): HtmlResponse
    {
        // ...
    }
}
```

If a route has no custom request class name defined, that HTTP action's `process()` method will be provided with an 
instance of `Noctis\KickStart\Http\Request\Request` as its `$request` parameter.

## Available Methods

A custom HTTP request class, as it extends the `Noctis\KickStart\Http\Request\Request`, offers implementation of all the 
methods the `Psr\Http\Message\ServerRequestInterface` interface declares, i.e.:

* `getAttribute()` - for fetching custom attributes set by HTTP middlewares or named route parameters,
* `getParsedBody()` - for fetching the request's body (used in requests sent _via_ HTTP's `POST` method),
* `getQueryParams()` - for fetching request's query parameters (used in requests sent _via_ HTTP's `GET` method),
* `getUploadedFiles()` - for getting a list of uploaded files, if there were any.

In addition to the above-mentioned methods, Kickstart's `Request` class offers two more, helper/shortcut methods:

* `fromQueryString()` - a wrapper method for the `getQueryParams()` method; returns the value of a given query string 
  parameter or the provided default value (`null` by default),
* `fromBody()` - a wrapper method for the `getParsedBody()` method; returns the value of a given parameter contained in
  the request's body or the provided default value (`null` by default).

## Custom Methods

A custom request class can have its own, additional methods added. For example, if one of your request's query string 
parameters is a string representing a date, named `date`, you could fetch it like so:

```php
/** @var string */
$date = $this->fromQueryString('date');
```

or you could add the following method to your custom request class:

```php
<?php

declare(strict_types=1);

namespace App\Http\Request;

use Noctis\KickStart\Http\Request\Request;
use Psr\Http\Message\ServerRequestInterface;

final class DummyRequest extends Request implements ServerRequestInterface
{
    // ...

    public function getDate(): \DateTimeImmutable
    {
        return new \DateTimeImmutable(
            $this->fromQueryString('date')
        );
    }
}
```

and then use it like so, in your action:

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
    // ...

    /**
     * @param DummyRequest $request
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var \DateTimeImmutable $date */
        $date = $request->getDate();

        // ...
    }
}
```
