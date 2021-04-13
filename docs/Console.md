# Console application

Files related to a console (CLI) application can be found in the `bin` and `src/Console` directories.

The `bin` folder contains the `console` file which is the "entry" point for all defined console commands.

The `src/Console/Command` folder contains all the possible commands that can be run.

Here's how it all works.

## The gist

The moment the following command is run withing the project's base directory:
```shell
php bin/console command:name
```

The `command:name` should be replaced by an appropriate command name. Each console command must have its own, unique one.

The system will then locate the appropriate command file in the `src/Console/Command` directory and call its `execute()`
method. `Kickstart` utilizes Symfony's [Console Component](https://symfony.com/doc/5.2/components/console.html) to run 
the commands.

## How to add a new command

To add a new command, one must:

* create a new class within the `src/Console/Command` directory, which extends the 
  `\Noctis\KickStart\Console\Command\AbstractCommand` class,
* define a name for it by adding a static `$defaultName` variable in the new class, e.g.:
  ```php
  protected static $defaultName = 'dummy:command';
  ```
* register the new class in the `bin/console` file, by adding its class name to the array passed to the 
  `Application`'s constructor:
  ```php
  [...]

  use App\Console\Command\DummyCommand;

  [...]

  $app = new Application([
      DummyCommand::class
  ]);
  ```

To run the command, one should execute the following command:
```shell
php bin/console dummy:command
```