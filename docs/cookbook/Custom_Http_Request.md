# Creating a Custom HTTP Request Class

If your HTTP action needs to read parameters passed to it through the HTTP request, you can add an instance of a request
object (represented by the `Symfony\Component\HttpFoundation\Request` class) into the `execute()` method, like so:

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

But, what if you wanted a specific kind of object to be returned? For example, say you wanted to be able to call:

```php
$request->getDate();
```

and get an instance of a `DateTimeImmutable` object?

Or, what if you wanted for one of the parameters to be a part of the URL? For example, if someone requests 
`/product/13/details` in their browser, you want to be able to call:

```php
$request->getProductID();
```

and get an integer of 13?

Both of these problems can be solved by creating a custom HTTP request object. The latter case, with a value embedded
into the URL, requires a specific HTTP route definition - we'll get to that later on.

## Query Parameters

Suppose your HTTP action is called `DummyAction`. Let's start by creating a class called `DummyRequest` class in the
`App\Http\Request` namespace, extending the abstract `Noctis\KickStart\Http\Request\AbstractRequest` class:

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

You can now call the `getDate()` method on the passed `$request` object and get an instance of `DateTimeImmutable` in
return.

## Named Parameters

One way of passing query parameters to the HTTP action is by embedding them into the URL itself, e.g.: 
`/product/13/details`. In this case, there's a value of `13` passed into the request. But, what's the name of the
parameter? How do you get that `13` from the request, what method should you call?

The name part is up to you. Let's name it `productID`. Add the following method to the `DummyRequest` class:

```php
final class DummyRequest extends AbstractRequest
{
    //...
    
    public function getProductID(): int
    {
        return $this->get('productID');
    }
}
```

The last thing we need to do is to modify the route pointing to `DummyAction` to expect within it a named parameter 
called `productID`.

Open `src/Http/Routing/routes.php` and add/modify the appropriate route definition:

```php
['GET', '/product/{productID:\d+}/details', DummyAction::class],
```

Now this route will expect a numeric (`\d+`) value within the URL, between the `product/` and `/details` parts.

Kickstart utilizes the [FastRoute](https://github.com/nikic/FastRoute) library for HTTP routing. If you want to know
more on how you can define URLs, check its [documentation](https://github.com/nikic/FastRoute#readme).
