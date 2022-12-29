# Removing Dummy (Example) Code

The `Kickstart` app comes with some dummy (example) code, to help you get started working on creating your own
application, without resorting to constantly going back and forth between the IDE and the documentation.

Some files can be removed outright, while some have to be modified by removing the references to the deleted ones.

This dummy code can be safely removed without breaking the application. Here's how to do it.

**WARNING: This guide assumes you have at least basic OOP-in-PHP knowledge. When a file needs to be modified, this guide 
will not explicitly state which lines need to be changed and how.**

Let's start by removing the database-related things. Delete the following files:

* `src/Database/Table/DummyTable.php`
* `src/Entity/DummyEntity.php`
* `src/Entity/DummyEntityInterface.php`
* `src/Repository/DummyRepository.php`
* `src/Repository/DummyRepositoryInterface.php`

Done. Now the services-related things. Delete the following files:

* `src/Provider/DummyServicesProvider.php`
* `src/Service/DummyService.php`
* `src/Service/DummyServiceInterface.php`

There are still some references to the `DummyServicesProvider` class. Modify the following files, by removing all
references to that class:

* `bin/console`
* `public/index.php`

Once that's done, you can remove the `DummyCommand` from the application. To do that, remove the 
`src/Console/Command/DummyCommand.php` file and remove references to the that class from the `bin/console` file.

Now for HTTP-related things - there's going be a few. Start by deleting the following files:

* `src/Http/Action/DummyAction.php`
* `src/Http/Middleware/Guard/DummyGuard.php`
* `src/Http/Request/DummyRequest.php`
* `templates/dummy.html.twig`
* `templates/layout.html.twig` (optionally)

Next remove the route referring to `DummyAction` and `DummyGuard` classes in the `config/routes.php` file.

And that's it. No more dummy code left in the application.

I know I could've created a script to delete & modify files appropriately, which you could've run from the command line,
but I lack the skill to write such a script, or at least one which wouldn't maim your application in the process. So,
for now you're on your own :)
