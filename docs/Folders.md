# Folders

Kickstart was designed to be a base for two kinds of PHP applications:

* those run from the terminal/command line (CLI), and
* those available from one's Internet browser.

An out-of-the-box Kickstart project has the following folder structure:

```
|- bin
|- config
|- docker
|- docs
|- |- cookbook
|  \- upgrading
|- public
|- src
|  |- Console
|  |  \- Command
|  |- Database
|  |  \- Table
|  |- Entity
|  |- Http
|  |  |- Action
|  |  |- Middleware
|  |  |  \- Guard
|  |  \- Request
|  |- Provider
|  |- Repository
|  \- Service
|- templates
|- var
|  \- cache
|     \- templates
\- vendor 
```

### `bin`

This folder contains one file: `console`. This file is the entry point to run any console command defined within your 
application. This file contains a list of all the enabled commands.

`console` should be run from the command line, like so: 

```shell
php bin/console command:name
```

### `config`

This folder contains the `routes.php` file with a list of routes for HTTP actions.

### `docker`

This folder contains additional [Xdebug](https://xdebug.org/) PHP configuration files, used for running your application
in Docker containers.

### `docs`

This folder contains all the documentation for Kickstart.

### `public`

This folder contains the file `index.php` which is the entry point for executing any HTTP actions defined within your 
application. This file (along with `.htaccess`) is an example of the "Front Controller" design pattern.

`.htaccess` contains directives for Apache's `mod_rewrite` module.

### `src/Console/Command`

This folder contains project's commands classes, available through the `bin/console` entry point. Each command should be 
represented by a single class. You can read more about console commands [here](Console.md).

### `src/Database/Table`

The classes within this folder contain meta-data related to database tables, for example: tables' names. The classes
within this folder should not be instantiated.

### `src/Entity`

This folder contains classes representing entities, i.e. things that can be uniquely identified by a specific identifier.
An entity class could represent a single identifiable row from a database table.

### `src/Http/Action`

This folder contains application's HTTP actions classes. Each route defined in the application's routes list 
([`config/routes.php`](../config/routes.php)) should have an HTTP action class assigned to handle the incoming HTTP
request and issue an HTTP response in return.

### `src/Http/Middleware/Guard`

This folder contains the application's optional HTTP guards, implemented as HTTP middleware. The classes may be assigned
to specific routes. If an incoming request is destined for an action with guards defined, said request will first be
handed over to those guards, in the order they were defined.

You can read more about guards [here](HTTP.md) (`Middleware` section, at the bottom).

### `src/Http/Request`

This folder contains optional classes representing requests, to be passed to HTTP actions, instead of Kickstart's 
standard request object. A custom request class may, for example contain parameter-specific getters, e.g. `getDate()`.

### `src/Provider`

This folder contains classes with definitions for the Dependency Injection Container. You can read more about Service
Providers [here](Service_Providers.md).

### `src/Repository`

This folder contains classes representing repositories. Usually repositories are dependent on a database connection,
but it doesn't need to be the case.

### `src/Service`

This folder contains classes for various services - in the broad meaning of the term - that your application may need.

### `templates`

This folder contains Twig templates, which are usually used to generate HTML, send back to the browser by the HTTP actions.

### `var`

It is recommended to use this folder for storing auto-generated files, which the user will have no interest in, i.e. 
cache files.

Out-of-the-box the `var/cache/templates` folder is used to store compiled Twig templates, which speeds up generating 
HTML.

### vendor

This folder contains all the 3rd party packages needed to run the Kickstart app, including Kickstart's core (system) 
package.
