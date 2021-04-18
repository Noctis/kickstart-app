# Kickstart Application

[![Latest Stable Version](https://poser.pugx.org/noctis/kickstart-app/v)](//packagist.org/packages/noctis/kickstart-app)
[![Latest Unstable Version](https://poser.pugx.org/noctis/kickstart-app/v/unstable)](//packagist.org/packages/noctis/kickstart-app)
[![Type Coverage](https://shepherd.dev/github/Noctis/kickstart-app/coverage.svg)](https://shepherd.dev/github/Noctis/kickstart-app)
[![License](https://poser.pugx.org/noctis/kickstart-app/license)](//packagist.org/packages/noctis/kickstart-app)

**DISCLAIMER:** Kickstart 2.0 is, for now, still in beta. I expect to release a final, stable version later in April 
2021. There's a 1.x version in the repository, but I would advise you not to install it.

This note will be removed once Kickstart 2.0 goes stable.

## What is it?

It's a skeleton/demo application part of the Kickstart project. The Kickstart project itself consists of two parts:

* the application part (this one),
* the system part - the [`noctis/kickstart`](https://github.com/Noctis/kickstart) package.

This application contains all the files the user should be able to modify in order to create their own Kickstart-based 
application.

## What's it good for?

Kickstart was created to be a base for building micro and small PHP applications, either Web- or CLI-based.  

## OK, so how do I install this thing?

**IMPORTANT:** Kickstart has two major platform requirements:

* PHP 8.0 (or higher),
* Composer 2.0 (or higher)

To create a new project Kickstart-based project, run the following command and let Composer do its thing:

```shell
composer create-project noctis/kickstart-app app-name --stability=dev
```

**IMPORTANT:** replace `app-name` in the command above with whatever name you want. `app-name` is the name of the folder 
which will be created in the current working directory.

## OK, it installed. Now what?

Now you're free to modify/add files in the `app-name` folder (or whatever you changed it to) to build your application. 
I've included some demo/dummy files within the project to help you get started. You're free to remove those files 
altogether if you have no use for them. Instructions on how to do that can be found 
[here](docs/cookbook/Removing_Dummy_Code.md).

**IMPORTANT:** If you're building a Web-based application, configure your WWW server to serve files from the `public` 
directory.

## Application Components

A fresh Kickstart-based project consists of a couple of things:

* Configuration,
* HTTP Actions (with templates/views),
* Console Commands,
* Service Providers,
* Database Repositories,
* Services.

## Configuration

The project's configuration can be found in the `.env` file, in its root directory.

**NEVER COMMIT THE `.env` FILE IN YOUR PROJECT - IT CONTAINS SENSITIVE INFORMATION WHICH MUST REMAIN PRIVATE!**

This is how the `.env` file looks by default:

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

Kickstart utilizes the Repository pattern, more or less. You call the appropriate methods on a repository object when
you want to fetch or store something from/to the database. 

By default, repository classes can be found in the `src/Repository` folder (the `App\Repository` namespace). All 
repositories which utilize the database connection extend the `Noctis\KickStart\Repository\AbstractDatabaseRepository` 
abstract class, which provides a `protected` field called `$db` representing the database connection.

Kickstart utilizes [ParagonIE's EasyDB](https://github.com/paragonie/easydb) package for running queries against the
database engine of your choice. EasyDB is a simple wrapper around PHP's PDO, which in my opinion is way easier to use
than PDI itself. If you want to know more on why I chose EasyDB and not a different library, check the [FAQ](docs/FAQ.md).

## Service Providers

You can read more about Service Providers [here](docs/Service_Providers.md).

## Folders

You can read more about what each folder in your project's directory is [here](docs/Folders.md).

## Updating

If you need to update the system part of your application, i.e. the `noctis/kickstart` package, just run:

```shell
composer update noctis/kickstart
```

Seeing as updating a Kickstart project is not as simple as that, I will do my best to update the `noctis/kickstart-app`
package (this one) as rarely as possible. When I do release a new version of it, I will provide instructions on how to
upgrade in a `UPDATING.md` file.

I will also keep the version numbers between both packages consistent. For example, when I make changes to the 
`noctis/kickstart` package that are incompatible with `noctis/kickstart-app` 2.x, I will release them as version 3.x
of the former, along with an updated 3.x version of the latter. This way a `composer update` will not break your 
Kickstart application.

## FAQ

Additional questions and answers relating to Kickstart can be found in the [FAQ](docs/FAQ.md).

## Recipes

* [Adding a New Database Repository](docs/cookbook/Adding_Database_Repository.md),
* [Adding a Second Database Connection](docs/cookbook/Adding_Second_Database_Connection.md)
* [Creating a Custom HTTP Request Class](docs/cookbook/Custom_Http_Request.md)
* [Creating a new HTTP Action](docs/cookbook/New_Http_Action.md)
* [Creating a New Console Command](docs/cookbook/New_Console_Command.md)
* [Removing the Database Connectivity Functionality](docs/cookbook/Removing_Database_Connectivity.md)
* [Removing Dummy (Example) Code](docs/cookbook/Removing_Dummy_Code.md)
* [Removing the HTTP Functionality](docs/cookbook/Removing_Http_Functionality.md)
* [Sending Files in Response](docs/cookbook/Sending_Files.md)