# Custom Console Command Loader

By default, all console commands are lazily loaded, i.e. the command class is instantiated only when the given command
is actually called. To do this, Kickstart uses Symfony's 
[`ContainerCommandLoader` class](https://symfony.com/doc/5.2/console/lazy_commands.html#containercommandloader). 

It is possible to use a different command loader. To do that, you can use the `setCommandLoaderFactory()` method:

```php
#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Console\Application;
use App\Console\Command\DummyCommand;
use App\Console\Service\CustomCommandLoader;
use DI\Container;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;

require_once __DIR__ . '/../bootstrap.php';

$app = new Application([]);
$app->setCommandLoaderFactory(
    function (Container $container): CommandLoaderInterface {
        return new CustomCommandLoader([
            'dummy:command' => DummyCommand::class
        ]);
    }
);
$app->run();
```

The `setCustomCommandLoaderFactory()` method accepts a single argument - a callable (factory method). Said callable:

* will be given one argument - an instance of `\DI\Container` (Kickstart's 
  [DIC of choice](https://packagist.org/packages/php-di/php-di)),
* must return an instance of a class implementing the `Symfony\Component\Console\CommandLoader\CommandLoaderInterface`.

**IMPORTANT:** If a custom command loader is used, the list of commands given to the `App\Console\Application` class,
through its constructor, will be ignored.
