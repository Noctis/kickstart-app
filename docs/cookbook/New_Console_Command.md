# Creating a New Console Command

To add a new command, you must:

* create a new class within the `src/Console/Command` directory, which extends the
  `Noctis\KickStart\Console\Command\AbstractCommand` class,
* define a name for it by adding a static `$defaultName` field in the new class, e.g.:
  ```php
  protected static $defaultName = 'dummy:command';
  ```
* register the new command in the `bin/console` file, by including an instance of it, in the array passed to the
  `ConsoleApplication`'s `setCommands()` method:
  ```php
  use App\Console\Command\DummyCommand;
  use Noctis\KickStart\Console\ConsoleApplication;
  
  // ...
  
  /** @var ConsoleApplication $app */
  $app = $container->get(ConsoleApplication::class);
  $app->setCommands([
      $container->get(DummyCommand::class)
  ]);
  $app->run();
  ```

To run the new command, execute the following command in your CLI, in the root folder of your application:
```shell
php bin/console dummy:command
```
