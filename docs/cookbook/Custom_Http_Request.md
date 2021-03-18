##Creating a custom HTTP request class

If your HTTP action needs to read parameters passed to it within the HTTP request, you can add an instance of
`\Symfony\Component\HttpFoundation\Request` class via the `execute()` method, like so:

```php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

public function execute(Request $request): Response
{
    //...
}
```

Having the `$request` object, you can now fetch parameters passed with the request by calling its `get()` method:

```php
$foo = $request->get('foo');
```

But, what if you wanted a specific kind of object to be returned? For example, say you wanted to be able to call

```php
$request->getDate();
```

and get an instance of a `\DateTimeImmutable` object?

Or, what if you wanted for one of the parameters to be a part of the URL? For example, if someone requests `/product/13/details`
in their browser, you want to be able to call

```php
$request->getProductID();
```

and get an integer of 13?

Both of these problems can be resolved by creating a custom HTTP request object. The latter case requires a specific
HTTP route definition, but we'll get to that later.

Suppose your HTTP action is called `DummyAction`. Let's start by creating a class called `DummyRequest` class in the
`\App\Http\Request` namespace, extending the abstract `\Noctis\KickStart\Http\Request\AbstractRequest` class:

```php
<?php

declare(strict_types=1);

namespace App\Http\Request;

use Noctis\KickStart\Http\Request\AbstractRequest;

final class DummyRequest extends AbstractRequest
{
}
```

Now, let's define within it the two methods that we need:

```php
<?php

declare(strict_types=1);

namespace App\Http\Request;

use DateTimeImmutable;
use Noctis\KickStart\Http\Request\AbstractRequest;

final class DummyRequest extends AbstractRequest
{
    public function getDate(): DateTimeImmutable
    {
        return $this->get('date');
    }
    
    public function getProductID(): int
    {
        return (int)$this->get('productID');
    }
}
```

Now let's make an object of `DummyRequest` class the dependency of the `DummyAction` HTTP action:

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
        //...
    }
}
```

The last thing we need to do, is to modify the route pointing to `DummyAction` to expect within it a parameter called
`productID`.

Open `src/Http/Routes/StandardRoutes` and modify the appropriate route definition:

```php
$r->addGroup(
    $baseHref,
    function (RouteCollector $r) {
        $r->get('/product/{productID}/details', DummyAction::class);
    }
);
```
