# Custom HTTP Requests

If you wish to add your own, additional methods to a request object, you can do so by creating a custom HTTP request
object, which acts as decorator around the original request. Your custom HTTP request class must extend the
`Noctis\KickStart\Http\Request\AbstractRequest` abstract class:

```php
<?php

declare(strict_types=1);

namespace App\Http\Request;

use Noctis\KickStart\Http\Request\AbstractRequest;

final class DummyRequest extends AbstractRequest
{
    public function getFoo(): string
    {
        return 'foo';
    }
}
```

You can then inject your custom HTTP request object into an HTTP action class, through its constructor:

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
    private DummyRequest $request;

    public function __construct(DummyRequest $request) 
    {
        $this->request = $request;
    }
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): HtmlResponse
    {
        // ...
    }
}
```

**IMPORTANT:** Please note that when using custom requests in your HTTP Action, you will have access to **two request
objects** in your action's `process()` method body: 

* `$request`, which is a standard object, implementing the `ServerRequestInterface` interface, and
* `$this->request`, which is your custom request object.

## Available Methods

A custom HTTP request class offers some of the most commonly uses methods defined by the 
`Psr\Http\Message\ServerRequestInterface` interface, i.e.:

* `getAttribute()` - for fetching custom attributes set by HTTP middlewares or named route parameters,
* `getParsedBody()` - for fetching the request's body (used in requests sent _via_ HTTP's `POST` method),
* `getQueryParams()` - for fetching request's query parameters (used in requests sent _via_ HTTP's `GET` method),
* `getUploadedFiles()` - for getting a list of uploaded files, if there were any.

Every custom HTTP request object offers one additional, helper method, for fetching request parameters: `get()`. This
method will try and return a parameter, of the given name, checking the request's contents in the following order:

* attributes,
* body (`POST`),
* query params (`GET`).

If nothing with the given name is found in the request, the `get()` method will return:

* the value provided as the method's second argument, or
* `null` if no second argument was given.

## Custom Methods

A custom request class can have its own, additional methods available. For example, if one of your request query 
parameters is a string representing a date, you could call `get()` like this:

```php
/** @var string */
$date = $this->request
    ->get('date');
```

or you could add the following method to your custom request class:

```php
public function getDate(): \DateTimeImmutable
{
    return new \DateTimeImmutable(
        $this->get('date')
    );
}
```

and then use it like so:

```php
/** @var \DateTimeImmutable */
$date = $this->request
    ->getDate();
```


