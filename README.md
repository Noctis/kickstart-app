# Kickstart Application

## What is it?

It's a skeleton/demo application based upon the [`noctis/kickstart`](https://github.com/Noctis/kickstart) package. This 
is the user part of the Kickstart project. It's the part of the Kickstart project containing all the files the user is
allowed to modify in order to create their own Kickstart-based application. 

## OK, so how do I install this thing?

Use Composer to create a new project, based on `kickstart-app`:

```shell
composer create-project noctis/kickstart-app app-name --repository='{"type":"vcs","url":"git@github.com:Noctis\/kickstart.git"}'
```

**IMPORTANT:** replace `app-name` in the command above with whatever name you want. `app-name` is the name of the folder 
which will be created in the current working directory.

## OK, it installed. Now what?

Now you're free to modify/add files in the `app-name` folder (or whatever you changed it to) to build your application. 
I've included some demo/dummy files within the project to help you get started. You're free to remove those files 
altogether if you have no use for them. Instructions on how to do that can be found 
[here](docs/cookbook/Removing_Dummy_Code.md).

## Application Components

A fresh Kickstart project consists of a couple of elements, some of which may be familiar to you:

* Configuration,
* HTTP Actions (with templates/views),
* Console Commands,
* Service Providers,
* Database Repositories,
* Services.

## Configuration

The project's configuration can be found in the `.env` file, in its root directory. A fresh project won't have this
file, but an example file called `.env-example` is provided, which should be copied over as or renamed to `.env` and 
then edited.

**NEVER COMMIT THE `.env` FILE IN YOUR PROJECT - IT CONTAINS SENSITIVE INFORMATION WHICH MUST REMAIN PRIVATE!**

This is how the `.env-example` file looks by default:

```dotenv
debug=false
# "/" for root-dir, "/foo" (without trailing slash) for sub-dir
basehref=/
db_host=localhost
db_user=dbuser
db_pass=dbpass
db_name=dbname
db_port=3306
```

Here's a rundown of what each of these options mean:

### `debug`

Setting this option to `false` changes two things:

* PHP error messages are hidden,
* templates (views) are cached; any changes made to them will NOT be visible upon refreshing the page in the browser.

Setting this option to `true` causes:

* PHP error messages to be displayed,
* templates (views) are not cached; any changes made to them will immediately be visible upon refreshing the page in the
  browser.

**This option should be set to `false` in production environments, and set to `true` during development.**

**If you're making changes to your templates/views and they're not showing up in your browser - either clear the cache,
by deleting the contents of the `var/cache/templates` directory, or set the `debug` option in `.env` to `true`.**

### `basehref`

This is the URI of where the application is available from the browser. If it's available at, for example:
`localhost`, the default value of `/` should be used. If it's available in a sub-directory, for example: 
`localhost/kickstart`, the `basehref` value should be set to `/kickstart`.

**IMPORTANT:** The `barehref` value in `.env` and the `RewriteBase` value in `public/.htaccess` file should always be 
the same! If those values are different, you'll be getting a lot of 404 errors.

For console commands the value of this parameter does not matter.

### `db_host`, `db_user`, `db_pass`, `db_name`, `db_port`

Those 5 options are the database credentials:

* `db_host` - host name for your database (e.g. `localhost`),
* `db_port` - port number (default: `3306`),
* `db_user` - database user name,
* `db_pass` - database user password,
* `db_name` - the name of the database.

## HTTP Actions

You can read more about HTTP actions [here](docs/HTTP.md).

## Console Commands

You can read more about Console Commands [here](docs/Console.md).

## Database Repositories

`Kickstart` utilizes the Repository pattern, more or less. You call the appropriate methods on a repository object when
you want to fetch or store something from/to the database. 

By default, repository classes can be found in the `src/Repository` folder (the `App\Repository` namespace). All 
repositories which utilize the database connection extend the `Noctis\KickStart\Repository\AbstractDatabaseRepository` 
abstract class, which provides a `protected` field called `$db` representing the database connection.

`Kickstart` uses [ParagonIE's EasyDB](https://github.com/paragonie/easydb) package for running queries against the
database engine of your choice. EasyDB is a simple wrapper around PHP's PDO. If you want to know more on why I chose
EasyDB and not a different library, check the [FAQ](docs/FAQ.md).

## Service Providers

You can read more about Service Providers [here](docs/Service_Providers.md).

## FAQ

Additional questions and answers relating to Kickstart can be found in the [FAQ](docs/FAQ.md).

## Recipes

* [Adding a New Database Repository](docs/cookbook/Adding_Database_Repository.md),
* [Adding a Second Database Connection](docs/cookbook/Adding_Second_Database_Connection.md)
* [Creating a Custom HTTP Request Class](docs/cookbook/Custom_Http_Request.md)
* [Creating a new HTTP Action](docs/cookbook/New_Http_Action.md)
* [Removing the Database Connectivity Functionality](docs/cookbook/Removing_Database_Connectivity.md)
* [Removing the HTTP Functionality](docs/cookbook/Removing_Http_Functionality.md)
