# Service Providers

Service providers in a Kickstart application act as a provider of [autowiring](https://php-di.org/doc/autowiring.html)
definitions for Kickstart`s DIC (Dependency Injection Container) - [PHP-DI 6](https://php-di.org/).

Each Service Provider implements the `Noctis\KickStart\Provider\ServicesProviderInterface` interface and has a `get()`
method which returns an array of definitions. Each key of this array should be an interface or class name, while each 
value should be one of the following:

* a class name,
* a callable (i.e. factory), returning an object,
* a valid [definition value](https://php-di.org/doc/php-definitions.html#definition-types) accepted by PHP-DI.

## Examples

For example, if there is an `App\Service\DummyServiceInterface` interface defined, a class called 
`App\Service\DummyService` which implements it, and you wish for DIC to provide an instance of the latter every time 
the former is requested via dependency injection (i.e. via constructor), this is how the entry in the array returned by 
the Service Provider should look like:

```php
use App\Service\DummyService;
use App\Service\DummyServiceInterface;

// ...

public function getServicesDefinitions(): array
{
    return [
        // ...
        DummyServiceInterface::class => DummyService::class,
        // ...
    ];
}
```

If you wish to define how the `App\Service\DummyService` instance is created, you can provide a callable (a factory).
In that case here's how the entry in the array returned by the Service Provider should look like:

```php
use App\Service\DummyService;
use App\Service\DummyServiceInterface;
use Psr\Container\ContainerInterface;

// ...

public function getServicesDefinitions(): array
{
    return [
        // ...
        DummyServiceInterface::class => function (ContainerInterface $container): DummyService {
            return new DummyService('foo');    
        },
        // ...
    ];
}
```

A callable can request an instance of the DIC, like in the above example (the `$container` variable).

If you wish to know more, for example how to let DIC create an instance of a requested object, while you provide one of
the constructor values yourself, consult PHP-DI's 
[documentation on the matter](https://php-di.org/doc/php-definitions.html#autowired-objects) or check the Service
Providers within the application (in the `src/Provider` directory) or the system (in the 
`vendor/noctis/kickstart/src/provider` directory of the application).

## Adding new Service Providers/Deleting existing

How does Kickstart know which Service Providers to use? If you create a new Service Provider class will the application
notice it right away? No, it won't. Each Service Provider needs to be registered in the application's entry point. A
standard Kickstart project has two entry points:

* `bin/console` - for console applications,
* `public/index.php` - for Web applications.

To register a new service provider, an instance of it must be passed to the `registerServicesProvider()` method of the
container builder.

Here's how a default `bin/console` file look like, which already registers a few service providers:

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Console\Command\DummyCommand;
use App\Provider\ConfigurationProvider;
use App\Provider\DatabaseConnectionProvider;
use App\Provider\DummyServicesProvider;
use App\Provider\RepositoryProvider;
use Noctis\KickStart\Console\ConsoleApplication;
use Noctis\KickStart\Console\ContainerBuilder;

require_once __DIR__ . '/../bootstrap.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder
    ->registerServicesProvider(new ConfigurationProvider())
    ->registerServicesProvider(new DatabaseConnectionProvider())
    ->registerServicesProvider(new DummyServicesProvider())
    ->registerServicesProvider(new RepositoryProvider())
;
$container = $containerBuilder->build();

/** @var ConsoleApplication $app */
$app = $container->get(ConsoleApplication::class);
$app->setCommands([
    DummyCommand::class
]);
$app->run();
```

And here's how the default `public/index.php` file looks like, which also registers some service providers:

```php
<?php

declare(strict_types=1);

use App\Provider\ConfigurationProvider;
use App\Provider\DatabaseConnectionProvider;
use App\Provider\DummyServicesProvider;
use App\Provider\HttpMiddlewareProvider;
use App\Provider\RepositoryProvider;
use Noctis\KickStart\Configuration\Configuration;
use Noctis\KickStart\Http\ContainerBuilder;
use Noctis\KickStart\Http\WebApplication;
use Noctis\KickStart\Provider\RoutingProvider;

require_once __DIR__ . '/../bootstrap.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder
    ->registerServicesProvider(new RoutingProvider(
        require_once __DIR__ . '/../src/Http/Routing/routes.php'
    ))
    ->registerServicesProvider(new ConfigurationProvider())
    ->registerServicesProvider(new DatabaseConnectionProvider())
    ->registerServicesProvider(new HttpMiddlewareProvider())
    ->registerServicesProvider(new DummyServicesProvider())
    ->registerServicesProvider(new RepositoryProvider())
;
if (Configuration::isProduction()) {
    /** @var string */
    $basePath = Configuration::get('basepath');
    $containerBuilder->enableCompilation($basePath . '/var/cache/container');
}

$container = $containerBuilder->build();

/** @var WebApplication $app */
$app = $container->get(WebApplication::class);
$app->run();
```