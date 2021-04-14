# Removing the Database Connectivity Functionality

A standard `Kickstart` project comes with built-in support for database connectivity. The application won't work until
some database credentials are provided in the `.env` file. They don't need to be valid, they just need to be defined.
But not all applications need a database connection to work properly. Here's what to do to get rid of it.

**WARNING: This guide assumes one has at least basic OOP-in-PHP knowledge. When a file needs to be modified, this guide
will not explicitly state which lines need to be changed and how.**

Edit `bootstrap.php` file and remove the following lines:

```php
'db_host'     => 'required',
'db_user'     => 'required',
'db_pass'     => 'required',
'db_name'     => 'required',
'db_port'     => 'required,int',
```

Now edit the `.env` configuration file (and `.env-example` to be thorough) are remove the above mentioned options. After
doing this, the application will stop complaining that those 5 options are not set in the configuration file.

Now lets get rid of the database-related classes. Delete the following directories:

* `src/Database`
* `src/Entity`
* `src/Repository`

Delete the `src/Provider/DatabaseConnectionProvider.php` file and remove the reference to it in the following files:

* `src/Console/Application.php`
* `src/Http/Application.php`

To make things extra clean, edit the following files:

* `src/Configuration/FancyConfiguration.php`
* `src/Configuration/FancyConfigurationInterface.php`

and remove the following methods from them:

* `getDBHost()`
* `getDBUser()`
* `getDBPass()`
* `getDBName()`
* `getDBPort()`
