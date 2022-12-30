# Console Application

Files related to a console (CLI) commands can be found in the `bin` and `src/Console` directories of your project. The 
`bin` folder contains the `console` file which is the "entry point" for the defined console commands, while the 
`src/Console/Command` folder contains all the possible commands that could possibly be run. 

The mere fact that a console command class exists does not mean that it is runnable. For that, the command needs to be 
registered (enabled), by adding a reference to it in the `bin/console` file.

Here's how it all works.

## The Gist

The moment the following command is executed within the project's base directory:
```shell
php bin/console command:name
```

the system will check all the console commands it knows (see inside `bin/console`) and if a command with a matching 
name - in this example: `command:name` - is found, that commands `execute()` method will be called. Each command must 
have its own, unique name.

Kickstart utilizes Symfony's [Console Component](https://symfony.com/doc/6.2/components/console.html) to run the console
commands.

## Command Lazy Loading (since Kickstart 2.2.0).

Starting with Kickstart 2.2.0, all console commands are 
[lazy-loaded](https://symfony.com/doc/6.2/console/lazy_commands.html) by default. It means that an instance of a 
command class will be created (instantiated) only when that specific command is called, via `php bin/console`. 

To do this, Kickstart uses Symfony's 
[`ContainerCommandLoader` class](https://symfony.com/doc/6.2/console/lazy_commands.html#containercommandloader). If you
wish to utilize a different command loader, like your own, you can use the `setCommandLoaderFactory()` method. You can 
read more about how to do that [here](cookbook/Custom_Console_Command_Loader.md).

## Recipes

* [Creating a New Console Command](cookbook/New_Console_Command.md)
* [Custom Console Command Loader](cookbook/Custom_Console_Command_Loader.md)
