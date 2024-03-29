# Creating a New Console Command

To add a new command, you must:

* create a new class within the `src/Console/Command` directory, which extends the
  `Noctis\KickStart\Console\Command\AbstractCommand` class, e.g.:
  ```php
  <?php
  
  declare(strict_types=1);
  
  namespace App\Console\Command;
  
  use Noctis\KickStart\Console\Command\AbstractCommand;

  final class DummyCommand extends AbstractCommand
  {
      // ...
  }
  ```
* define a name for it by adding an `AsCommand` 
  [attribute](https://www.php.net/manual/en/language.attributes.overview.php) to the class, e.g.:
  ```php
  <?php
  
  declare(strict_types=1);
  
  namespace App\Console\Command;
  
  use Noctis\KickStart\Console\Command\AbstractCommand;
  use Symfony\Component\Console\Attribute\AsCommand;
  
  #[AsCommand(name: 'dummy:command')]
  final class DummyCommand extends AbstractCommand
  {
      // ...
  }
  ```
* register the new command in the `bin/console` file, by including its fully qualified class name, in the array passed 
  to the `ConsoleApplication`'s `setCommands()` method:
  ```shell
  #!/usr/bin/env php
  <?php
  
  declare(strict_types=1);
  
  use App\Console\Command\DummyCommand;
  use Noctis\KickStart\Console\ConsoleApplication;
  use Noctis\KickStart\Http\Routing\RouteInterface;
  // ...
  
  require_once __DIR__ . '/../bootstrap.php';
  
  $app = ConsoleApplication::boot(
      //...
  );
  
  /** @var list<RouteInterface> $routes */
  $routes = require_once $_ENV['basepath'] . '/config/routes.php';
  $app->setRoutes($routes);
  
  $app->setCommands([
      DummyCommand::class,
  ]);
  $app->run();
  ```

To run the new command, execute the following command in your CLI, in the root folder of your application:
```shell
php bin/console dummy:command
```
