# Implementing Session Handling

If you wish for your application to utilize [sessions](https://www.php.net/manual/en/book.session), you can use the
[`laminas/laminas-session`](https://github.com/laminas/laminas-session) package.

You can read more about how to use this package [here](https://docs.laminas.dev/laminas-session/).

To install it, execute the following command at the root directory of your application:

```shell
$ composer require laminas/laminas-session
```

You should see Composer modifying your `composer.json`/`composer.lock` files, but not install any new files. This is 
fine. This is because `laminas/laminas-session` is one of Kickstart's own dependencies, meaning it's already installed. 
Kickstart does not offer any methods that expose it, so it's up to you to integrate it into your application.

To do so, register Laminas' `Laminas\Session\SessionManager` class in one of the application's existing 
[service providers](docs/Service_Providers.md), or in a new one. Here's an example of the latter:

```php
<?php

declare(strict_types=1);

namespace App\Provider;

use Laminas\Session\ManagerInterface;
use Laminas\Session\SessionManager;
use Noctis\KickStart\Provider\ServicesProviderInterface;

final class SessionProvider implements ServicesProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getServicesDefinitions(): array
    {
        return [
            ManagerInterface::class => SessionManager::class,
        ];
    }
}
```

If you do end up creating a new service provider file, don't forget to register it in your `public/index.php` file:

```php
<?php

declare(strict_types=1);

// ...
use App\Provider\SessionProvider;
use Noctis\KickStart\Http\WebApplication;

require_once __DIR__ . '/../bootstrap.php';

$app = WebApplication::boot([
    // ...
    new SessionProvider()
]);

// ...
```

There's no need to add the new service provider to the `bin/console` file, as sessions are not available in CLI 
applications.
