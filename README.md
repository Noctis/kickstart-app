#Kickstart-app

##What is it?

It's a skeleton/demo application based upon the `noctis/kickstart` package. This is the "user space" part of the 
Kickstart project. This project contains the files that the user should modify in order to create their own,
Kickstart-based application.

##OK, so how do I install this thing?

Use Composer to create a new project, based on `kickstart-app`:

```shell
composer create-project noctis/kickstart-app app-name --repository='{"type":"vcs","url":"git@bitbucket.org:NoctisPL\/kickstart-app.git"}'
```

**IMPORTANT:** replace `app-name` in the command above with whatever name you want. `app-name` is the name of the folder 
which will be created in the current working directory.

##OK, it installed. Now what?

Now you're free to modify/add files in the `app-name` (or whatever you changed it to) to build your application. I've
included some demo/dummy files within the project to help you get started. You're free to remove those files altogether
if you have no use for them. Instructions on how to do that can be found here (TODO: add a recipe on how to remove
demo/dummy files).

##What you need to know

A fresh `kickstart-app` project comes with a few elements which may or may not seem familiar to you:

* Configuration,
* HTTP Actions,
* Console Commands,
* Service Providers
* Database Repositories
* Services
* HTML templates (views)

## Configuration

Application's configuration can be found in the `.env` file, in the root directory. A fresh project won't have this
file, but an example file - `.env-example` file is provided, which should be copied over as `.env`.

**NEVER COMMIT THE `.env` FILE IN YOUR PROJECT - IT CONTAINS SENSITIVE INFORMATION WHICH SHOULD REMAIN PRIVATE!**

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

Here's a rundown of what all these options mean:

### debug

Setting this option to `false` changes two things:

* PHP errors are hidden,
* templates (views) are cached; any changes made to them will NOT be visible upon refreshing the page in the browser.

Setting this option to `true` causes:

* PHP errors to be displayed,
* templates (views) are not cached; any changes made to them will immediately be visible upon refreshing the page in the
  browser.

This option should be set to `false` in production environments, and set to `true` - during development.

**If you're making changes to your templates/views and they're not showing up in the browser - either clean the cache,
by deleting the contents of the `var/cache/templates` directory, or set the `debug` option value to `true`.**

### basehref

This is the URI of where the application is available from the browser. If it's available under, for example:
`localhost`, the default value of `/` should be used. If it's available at, for example: `localhost/kickstart`, the
`basehref` should be set to `/kickstart`.

**IMPORTANT:** The `barehref` value in `.env` and the `RewriteBase` value in `public/.htaccess` file should always be the 
same! If those values are different, you are most likely be getting a lot of 404 errors.

For console commands, the value of this parameter does not matter.

### db_host, db_user, db_pass, db_name, db_port

Those 5 options are the database connectivity credentials:

* `db_host` - host name for your RDB (e.g. MariaDB),
* `db_port` - port number (default: `3306`),
* `db_user` - database user name,
* `db_pass` - database user password,
* `db_name` - the name of the database.

## HTTP Actions

You can read more about HTTP actions [here](docs/HTTP.md).

## Console Commands:

You can read more about Console Commands [here](docs/Console.md).

## Database Repositories

`Kickstart` utilizes the Repository pattern, more or less. You call the repository object if you want to fetch or store
something from the database. 

By default, repository classes can be found in the `src/Repository` folder (`\App\Repository`) namespace. All 
repositories which contact the database extend the `\Noctis\KickStart\Repository\AbstractDatabaseRepository` abstract
class, which provides one `protected` field called `$db`, which represents a database connection.

`Kickstart` uses the [ParagonIE's EasyDB](https://github.com/paragonie/easydb) package for running queires against the
database engine of your choice. EasyDB is a simple wrapper around PHP's PDO. I picked it for two reasons:

* it's way easier to wrap your head round than PDO is,
* it is as secure as it can get, seeing as it was created by the [Paragon Initiative](https://paragonie.com/).

### Recipes

* [Adding a New Database Repository](docs/cookbook/Adding_Database_Repository.md),
* [Adding a Second Database Connection](docs/cookbook/Adding_Second_Database_Connection.md)





A freshly created `kickstart-app` project has:

* a single web page,
* a single console command,
* requires a working database connection.

###Dummy web page

Demo/dummy web page's code can be found in the `src/Http/Action/DummyAction.php` file:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Http\Request\DummyRequest;
use Noctis\KickStart\Http\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Response;

final class DummyAction extends AbstractAction
{
    public function execute(DummyRequest $request): Response
    {
        $name = $request->get('name') ?: 'World';

        return $this->render('dummy.html.twig', [
            'name' => $name,
            'foo'  => $request->getFoo(),
        ]);
    }
}
```

It takes an optional named argument from the request, called `name`:
```php
$name = $request->get('name') ?: 'World';
```

The action itself returns a rendered twig template named `dummy.html.twig', located in the `templates` folder of the 
project:

```php
return $this->render('dummy.html.twig', [
    'name' => $name,
    'foo'  => $request->getFoo(),
]);
```

As you can see, the `dummy.html.twig` gets passed two parameters: `name` and `foo`, both of which are taken from the
request. A request parameter can be obtained in one of two ways:

* by calling the `get()` method on it, or
* by utilizing a custom request class and defining a custom getter method in it.

`DummyAction` uses a custom request class, which offers both ways of getting request parameters. In this case we have
the `\App\Http\Request\DummyRequest` custom request class:

```php
<?php

declare(strict_types=1);

namespace App\Http\Request;

use App\Service\DummyServiceInterface;
use Noctis\KickStart\Http\Request\AbstractRequest;
use Symfony\Component\HttpFoundation\Request;

final class DummyRequest extends AbstractRequest
{
    private DummyServiceInterface $dummyService;

    public function __construct(DummyServiceInterface $dummyService, Request $request)
    {
        parent::__construct($request);

        $this->dummyService = $dummyService;
    }

    public function getFoo(): string
    {
        return $this->dummyService
            ->foo();
    }
}
```

The `DummyRequest` custom request class utilizes Kickstart's Dependency Injection Container (DIC), which you can read
more about here (TODO: add a link to instructions on using DIC).

Once a request in made in the web browser to a specific URL, Kickstart calls the `DummyAction::execute()` method and
returns whatever the method returned (a `\Symfony\Component\HttpFoundation\Response` type object or its subtype).

The routes, linking URLs to specific actions, are defined in the `\App\Http\Routes\StandardRoutes` class:

```php
<?php

declare(strict_types=1);

namespace App\Http\Routes;

use App\Configuration\FancyConfigurationInterface;
use App\Http\Action\DummyAction;
use App\Http\Middleware\Guard\DummyGuard;
use FastRoute\RouteCollector;
use Noctis\KickStart\Http\Routing\HttpRoutesProviderInterface;

final class StandardRoutes implements HttpRoutesProviderInterface
{
    private FancyConfigurationInterface $configuration;

    public function __construct(FancyConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function get(): callable
    {
        return function (RouteCollector $r): void {
            $baseHref = $this->configuration
                ->getBaseHref();

            $r->addGroup(
                $baseHref,
                function (RouteCollector $r) {
                    $r->get('/[{name}]', [
                        DummyAction::class,
                        [
                            DummyGuard::class,
                        ],
                    ]);
                }
            );
        };
    }
}
```

Here's the part that interests you:

```php
function (RouteCollector $r) {
    $r->get('/[{name}]', [
        DummyAction::class,
        [
            DummyGuard::class,
        ],
    ]);
}
```

We have one route defined here: `/`, followed by an optional string. Whatever string after the `/` will be available
from the request object as the `name` request parameter.



###Dummy command

###Database connection