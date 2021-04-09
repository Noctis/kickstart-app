# Service Providers

Service providers in a `Kickstart` application act as a provider of [autowiring](https://php-di.org/doc/autowiring.html)
definitions for `Kickstart`'s DIC (Dependency Injection Container) of choice - [PHP-DI 6](https://php-di.org/).

Each service provider implements the `Noctis\KickStart\Provider\ServicesProviderInterface` interface and has a `get()`
method which return an array. Each key of this array should be an interface or class name, while each value should be
one of the following:

* a class name,
* a callable (i.e. factory), returning an object,
* a valid [definition value](https://php-di.org/doc/php-definitions.html#definition-types) accepted by PHP-DI.

## Examples

For example, if there is an `\App\Service\DummyServiceInterface` interface defined, a class called 
`\App\Service\DummyService` which implements it, and you wish for DIC to provide an instance of the latter every time 
the former is required, this is how the entry in the array returned by the Service Provider should look like:

```php
return [
    // ...
    [App\Service\DummyServiceInterface => App\Service\DummyService],
    // ...
];
```

If you wish to define how the `\App\Service\DummyService` instance is created, you can provide a callable (a factory),
the entry in the array could look like this:

```php
use Psr\Container\ContainerInterface;

// ...

return [
    // ...
    [App\Service\DummyServiceInterface => function (ContainerInterface $container) {
        return new DummyService('foo');    
    }],
    // ...
];
```

A callable can request an instance of the DIC, like in the above example (the `$container` variable).

If you wish to know more, for example how to let DIC create an instance of a requested object, while you provide one of
the constructor values yourself, consult PHP-DI's 
[documentation on the matter](https://php-di.org/doc/php-definitions.html#autowired-objects) or check the Service
Providers within the application (in the `src/Provider` directory) or the system (in the 
`vendor/noctis/kickstart/src/provider` directory of the application).

## Adding new Service Providers/Deleting existing

How does `Kickstart` know which Service Providers to use? If you create a new Service Provider will the application
notice it right away? No, it won't. Each Service Provider needs to be registered in the `Application` class.

Kickstart provides two `Application` classes - one for console (CLI) applications, one for HTTP-based applications.

A list of Service Providers in use by any of these can be found in their respective files:

* `src/Console/Application.php` for console applications,
* `src/Http/Application.php` for HTTP-based application.
