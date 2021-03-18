Kickstart was designed to be a base for two kinds of PHP applications:

* those run from the terminal/command line (CLI), and
* those available from one's Internet browser.

An out-of-the-box Kickstart app has the following folder structure:

```
|- bin
|- docs
|- public
|- src
|  |- Configuration
|  |- Console
|  |  \- Command
|  |- Database
|  |  \- Table
|  |- Entity
|  |- Http
|  |  |- Action
|  |  |- Middleware
|  |  |  \- Guard
|  |  |- Request
|  |  \- Routes
|  |- Provider
|  |- Repository
|  \- Service
|- templates
|- var
\- vendor 
```

### /bin

This folder contains one file: `console`. This file is the entry point to run any console command defined
within your application. This file contains a list of all the available commands.

`console` should be run from the command line: `php bin/console ...`

### docs

This folder contains all the documentation for Kickstart.

### public

This folder contains the file `index.php` which is the entry point for executing any HTTP actions defined
within your application. This file (along with `.htaccess`) is an example of the "Front Controller" design pattern.

`.htaccess` contains directives for Apache's `mod_rewrite` module.

### src/Configuration

This folder contains the `FancyConfiguration` class which extends Kickstart's base `Configuration` class. Both
allow one to access application's configuration, defined in the `.env` file.

The difference between `FancyConfiguration` and the base `Configuration` classes is that the former allows one
to define option-specific methods, like `getDbHost()`, while the latter offers just the basic ones, eg. `get('db_host')`.

### src/Console/Command

This folder contains application's commands classes, available through the `bin/console` entry point. Each command
should be represented by one class.

### src/Database/Table

The classes within this folder contain meta-data related to database tables, for example: tables' names. The classes
within this folder should not be instantiated.

### src/Entity

This folder contains classes representing entities, ie. things that can be uniquely identified by a certain identifier.
An entity class could represent a single identifiable row from a database table.

### src/Http/Action

This folder contains application's HTTP actions classes. Each route defined in the application's routes list should
have an HTTP action class assigned to handle the incoming HTTP request and issue an HTTP response in return.

### src/Http/Middleware/Guard

This folder contains the application's optional HTTP guards, implemented as HTTP middleware. The classes may be assigned
to specific routes. If an incoming request is destined for an action with guards defined, said request will first be
handed over to those guards, in the order they were defined.

An HTTP guard may either:

* pass the given request further, to the next guard or the appropriate HTTP action, or
* generate and return a response.

In the latter case, the HTTP action will not be called.

### src/Http/Request

This folder contains optional classes representing requests, to be passed (as dependencies) to HTTP actions. A standard
Request class contains generic `get()` method for fetching request's parameters, while a custom one may contain
parameter-specific ones, e.g. `getDate()`.

### src/Http/Routes

This folder contains a class with definitions for the application's HTTP routes and which HTTP actions should handle
their incoming requests.

### src/Provider

This folder contains classes with definitions for the Dependency Injection Container.

### src/Repository

This folder contains classes representing repositories. Usually repositories are dependent on a database connection,
but it doesn't need to be the case.

### src/Service

This folder contains classes for various services - in the broad meaning of the term - that your application may need.

### templates

This folder contains Twig templates, which are usually used to generate HTML, send back to the browser by the HTTP actions.

### var

It is recommended to use this folder for storing auto-generated files, which the user will have no  interest in,
i.e. cache files.

Out-of-the-box the `cache` folder inside this one is used to store compiled Twig templates, which speeds up generating HTML.

### vendor

This folder contains all the 3-rd party packages needed to run the Kickstart app, including Kickstart's core (system) package.


Kickstart uses a Dependency Injection Container (DIC). That means that if a class you've created
needs dependencies, for example it needs a database connection, you should allow DIC to provide it,
instead of creating an instance of the dependency yourself.

The standard "entry point" for dependencies in the class' constructor.

