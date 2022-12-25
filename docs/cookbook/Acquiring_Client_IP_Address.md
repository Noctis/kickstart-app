# Acquiring Client IP Address

If you wish to acquire the client's IP address (assuming an HTTP request is currently being handled), you can install
the [`middlewares/client-ip`](https://github.com/middlewares/client-ip) middleware, which can register the found  IP
address in the request object. Here's a detailed instruction on how to do it.

First, install the `middlewares/client-ip` package. Execute the following command at the root directory of your 
application:

```shell
$ composer require middlewares/client-ip
```

Now, edit the routes list (`config/routes.php` file by default) and add `Middlewares\ClientIp` to the
middlewares list of the appropriate action. For example, if you wish that the `App\Http\Action\DummyAction` could fetch
the client's IP address from the request object:

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

modify the route so that it looks like so:

```php
<?php

declare(strict_types=1);

use App\Http\Action\DummyAction;
use App\Http\Middleware\Guard\DummyGuard;
use Middlewares\ClientIp;
use Noctis\KickStart\Http\Routing\Route;

return [
    Route::get('/', DummyAction::class, [DummyGuard::class, ClientIp::class]),
];
```

In this case, we've added the `Middlewares\ClientIp` middleware as the second one for the `DummyAction`, after the 
`App\Http\Middleware\Guard\DummyGuard` one.

If your action does not utilize any middlewares:

```php
<?php

declare(strict_types=1);

use App\Http\Action\DummyAction;
use Noctis\KickStart\Http\Routing\Route;

return [
    Route::get('/', DummyAction::class),
];
```

add the `Middlewares\ClientIp` as the only one, like so:

```php
<?php

declare(strict_types=1);

use App\Http\Action\DummyAction;
use Middlewares\ClientIp;
use Noctis\KickStart\Http\Routing\Route;

return [
    Route::get('/', DummyAction::class, [ClientIp::class]),
];
```

From now on, the client's IP address will be available in the request object, as an attribute called `client-ip`. You
can access it in your action like so:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Laminas\Diactoros\Response\HtmlResponse;
use Noctis\KickStart\Http\Action\ActionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DummyAction implements ActionInterface
{
    // ...
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): HtmlResponse
    {
        $clientIP = $request->getAttribute('client-ip');
        
        // ...
    }
}
```
