# Creating a New Console Command

To add a new command, one must:

* create a new class within the `src/Console/Command` directory, which extends the
  `Noctis\KickStart\Console\Command\AbstractCommand` class,
* define a name for it by adding a static `$defaultName` field in the new class, e.g.:
  ```php
  protected static $defaultName = 'dummy:command';
  ```
* register the new command in the `bin/console` file, by adding a reference its class name to the array passed to the
  `Application`'s constructor:
  ```php
  [...]

  use App\Console\Command\DummyCommand;

  [...]

  $app = new Application([
      //...
      DummyCommand::class
  ]);
  ```

To run the new command, execute the following command:
```shell
php bin/console dummy:command
```
