# Console Application

Files related to a console (CLI) commands can be found in the `bin` and `src/Console` directories of your project. The 
`bin` folder contains the `console` file which is the "entry point" for the defined console commands, while the 
`src/Console/Command` folder contains all the possible commands that can be run. 

The fact that a console command class exists does not mean that it can be run. It needs to be enabled, by adding a
reference to it in the `bin/console` file.

Here's how it all works.

## The gist

The moment the following command is executed within the project's base directory:
```shell
php bin/console command:name
```

the system will check all the enabled console commands (see inside `bin/console`) and if a command with a matching name - in
this example: `command:name` - is found, that commands `execute()` method will be called. Each command must have its own,
unique name.

Kickstart utilizes Symfony's [Console Component](https://symfony.com/doc/5.2/components/console.html) to run the console
commands.

## Recipes

* [Creating a New Console Command](cookbook/New_Console_Command.md)
