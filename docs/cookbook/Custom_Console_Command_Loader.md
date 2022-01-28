# Custom Console Command Loader

By default, all console commands are lazily loaded, i.e. the command class is instantiated only when the given command
is actually called. To do this, Kickstart uses Symfony's 
[`ContainerCommandLoader` class](https://symfony.com/doc/5.3/console/lazy_commands.html#containercommandloader). 

It is possible to use a different command loader. To do that, you can use the 
`ConsoleApplication`'s `setCommandLoaderFactory()` method:

```shell
#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Console\Command\DummyCommand;
use App\Console\Loader\CustomConsoleCommandLoader;
use Noctis\KickStart\Console\ConsoleApplication;
use Noctis\KickStart\Console\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;

require_once __DIR__ . '/../bootstrap.php';

$containerBuilder = new ContainerBuilder();
// ...
$container = $containerBuilder->build();

/** @var ConsoleApplication $app */
$app = $container->get(ConsoleApplication::class);
$app->setCommandLoaderFactory(
    function (ContainerInterface $container): CommandLoaderInterface {
        $commandLoader = new CustomConsoleCommandLoader($container);
        $commandLoader->setCommandMap([
                'dummy:command' => DummyCommand::class,
            ]);

        return $commandLoader;
    }
);
$app->run();
```

The `setCustomCommandLoaderFactory()` method accepts a single argument - a callable (factory method). Said factory 
method callable:

* will be given one argument - an instance of a Dependency Injection container implementing the 
  `\Psr\Container\ContainerInterface`,
* must return an instance of a class implementing the `Symfony\Component\Console\CommandLoader\CommandLoaderInterface`.

Here's an example of a custom console command loader class, implementing the aforementioned interface:

```php
<?php

declare(strict_types=1);

namespace App\Console\Loader;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;

final class CustomConsoleCommandLoader implements CommandLoaderInterface
{
    private ContainerInterface $container;

    /** @var array<string, class-string<Command>>  */
    private array $commandMap = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param array<string, class-string<Command>> $commandMap
     */
    public function setCommandMap(array $commandMap): void
    {
        $this->commandMap = $commandMap;
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): Command
    {
        if (!$this->has($name)) {
            throw new CommandNotFoundException(
                sprintf('Command "%s" does not exist.', $name)
            );
        }

        /** @var Command */
        return $this->container
            ->get($this->commandMap[$name]);
    }

    /**
     * @inheritDoc
     */
    public function has(string $name): bool
    {
        return isset($this->commandMap[$name]) && $this->container->has($this->commandMap[$name]);
    }

    /**
     * @inheritDoc
     */
    public function getNames()
    {
        return array_keys($this->commandMap);
    }
}
```

**IMPORTANT:** If a custom command loader is used, the list of commands given to the `ConsoleApplication::setCommands()`
method will be ignored.