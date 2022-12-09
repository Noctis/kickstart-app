# Custom HTTP Requests

If you wish to add your own, additional methods to a request object, you can do so by creating a custom HTTP request
object, which acts as decorator around the original request. Your custom HTTP request class must extend the
`Noctis\KickStart\Http\Request\Request` class and implement [PSR-7's](https://www.php-fig.org/psr/psr-7/) 
`Psr\Http\Message\ServerRequestInterface` interface:

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

You must then provide your custom request's class name in the route definition for your action. Routes list can be found
in your application's `src/Http/Routing/routes.php` file. Custom request's class name must be provided as the route's
4th argument, for example:

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

An instance of your custom request's class will be provided to the HTTP action through its `process()` method, as the
`$request` variable:

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

If a route has no custom request class name declared, that route's HTTP action will be provided with an instance of
`Noctis\KickStart\Http\Request\Request` class.

## Available Methods

A custom HTTP request class, as it extends the `Noctis\KickStart\Http\Request\Request`, offers implementation of all the 
methods the `Psr\Http\Message\ServerRequestInterface` interface declares, i.e.:

* `getAttribute()` - for fetching custom attributes set by HTTP middlewares or named route parameters,
* `getParsedBody()` - for fetching the request's body (used in requests sent _via_ HTTP's `POST` method),
* `getQueryParams()` - for fetching request's query parameters (used in requests sent _via_ HTTP's `GET` method),
* `getUploadedFiles()` - for getting a list of uploaded files, if there were any.

## Custom Methods

A custom request class can have its own, additional methods available. For example, if one of your request query 
parameters is a string representing a date, named `date`, you could fetch it like so:

```php
/** @var string */
$date = $this->getQueryParams()['date'];
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
            $this->getQueryParams()['date']
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
