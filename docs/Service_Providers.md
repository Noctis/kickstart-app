# Service Providers

## Definition

Service providers act as a registry, which Kickstart uses to build the DIC - the 
[Dependency Injection Container](https://medium.com/tech-tajawal/dependency-injection-di-container-in-php-a7e5d309ccc6). 
DIC automatically provides classes with any dependencies they required, so that they may be properly instantiated.

## Requirements

Service providers are usually found in the application's `src/Provider` directory. Every service provider class must:

* implement the `Noctis\KickStart\Provider\ServicesProviderInterface`,
* its `getServicesDefinitions()` method must return an array of key-value pairs, where:
  * key is a fully-qualified class name (recommended), or a unique string identifier,
  * value is a reference to that key's implementation.

For a service provider to be actually used, it needs to be registered in the application's entry points, i.e. the 
`bin/console` and/or `public/index.php` files.

## Examples

Say your HTTP action - `App\Http\Action\DummyAction` - requires an implementation of the 
`App\Service\DummyServiceInterface` interface:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Service\DummyServiceInterface;
use Noctis\KickStart\Http\Action\ActionInterface;

final class DummyAction implements ActionInterface
{
    private DummyServiceInterface $service;

    public function __construct(DummyServiceInterface $service)
    {
        $this->service = $service;
    }

    // ...
}
```

When the action is about to be instantiated, DIC will inspect its constructor and try to provide an implementation of
that interface. There exists one implementation of that interface - the `App\Service\DummyService` class:

```php
<?php

declare(strict_types=1);

namespace App\Service;

final class DummyService implements DummyServiceInterface
{
    // ...
}
```

To inform DIC that when some class needs an implementation of `DummyServiceInterface`, they should be provided with an 
instance of `DummyService`, the following definition should be added to a service provider:

```php
<?php

declare(strict_types=1);

namespace App\Provider;

use App\Service\DummyService;
use App\Service\DummyServiceInterface;
use Noctis\KickStart\Provider\ServicesProviderInterface;

final class DummyServicesProvider implements ServicesProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getServicesDefinitions(): array
    {
        return [
            DummyServiceInterface::class => DummyService::class,
            // ...
        ];
    }
}
```

Now, let's say that the `DummyService` class has its own dependency, but it's not a class or an interface, but a 
primitive value, an integer in this case:

```php
<?php

declare(strict_types=1);

namespace App\Service;

final class DummyService implements DummyServiceInterface
{
    private int $limit;

    public function __construct(int $limit)
    {
        $this->limit = $limit;
    }

    // ...
}
```

How do you tell the DIC, what should it provide as the `$limit` value when instantiating the `DummyService` class?

If you want to specify a specific value, for a specific constructor parameter, your service definition should look 
like so:

```php
<?php

declare(strict_types=1);

namespace App\Provider;

use App\Service\DummyService;
use App\Service\DummyServiceInterface;
use Noctis\KickStart\Provider\ServicesProviderInterface;

use function Noctis\KickStart\Service\Container\autowire;

final class DummyServicesProvider implements ServicesProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getServicesDefinitions(): array
    {
        return [
            DummyServiceInterface::class => autowire(DummyService::class)
                ->constructorParameter('limit', 5),
            // ...
        ];
    }
}
```

The definition above should be read as: _when someone requires an implementation of `DummyServiceInterface`, they should
be provided with an instance of `DummyService`, instantiated using `5` as that implementation's `$limit` constructor 
parameter value_.

You don't need to provide every single constructor's parameter value like this, only those for which DIC won't be able
to automatically guess values for.

## Advanced Examples

Suppose you have a repository which requires a database connection:

```php
<?php

declare(strict_types=1);

namespace App\Repository;

use ParagonIE\EasyDB\EasyDB;

final class DummyRepository implements DummyRepositoryInterface
{
    private EasyDB $db;

    public function __construct(EasyDB $db)
    {
        $this->db = $db;
    }

    // ...
}
```

Normally that wouldn't be a problem, but your application has 
[more than one database connections defined](cookbook/Adding_Second_Database_Connection.md) and they're registered in
the DIC under names: `primary_db` and `secondary_db`, instead of class names. How do you tell the DIC to provide this
repository with the `secondary_db` database connection?

Use the following service definition, using the definition helpers returned by the `autowire()` and `reference()`
functions:

```php
<?php

declare(strict_types=1);

namespace App\Provider;

use App\Repository\DummyRepository;
use App\Repository\DummyRepositoryInterface;
use Noctis\KickStart\Provider\ServicesProviderInterface;

use function Noctis\KickStart\Service\Container\autowire;
use function Noctis\KickStart\Service\Container\reference;

final class RepositoryProvider implements ServicesProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getServicesDefinitions(): array
    {
        return [
            DummyRepositoryInterface::class => autowire(DummyRepository::class)
                ->constructorParameter(
                    'db',
                    reference('secondary_db')
                ),
            // ...
        ];
    }
}
```

There is also one more definition helper available, via the: `decorator()` function. This can be used to 
"decorate"/"extend" a service definition already registered in the DIC. This can be used to, for example 
[add custom functions to the Twig template engine](cookbook/Custom_Twig_Function.md).
