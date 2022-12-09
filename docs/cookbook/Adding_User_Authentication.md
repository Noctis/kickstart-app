# Adding User Authentication

The following recipe describes step-by-step how to add simple user authentication to your Kickstart application. All
the user information will be stored in a `.htpasswd` file and the application will have a custom sign-in form.

The `App\Service\Security\AuthServiceInterface` interface will offer a few basic methods which use can use:

* `isSignedIn()` - tells you whether you're dealing with a signed-in user (`true`), or an anonymous one (`false`),
* `signOut()` - causes the currently signed-in user to be signed-out, forgotten so to speak,
* `getIdentity()` - returns the username of the currently signed-in user, or `null` if we're dealing with an
  anonymous one; you can modify this method to return an object (doesn't have to be a string).

## Dependencies

Firstly, install the two required dependencies:

```shell
$ composer require laminas/laminas-authentication:^2.11 laminas/laminas-crypt:^3.8
```

## Services

Create a folder called `Security` inside the `src/Service` directory. Inside it, create:

* `AuthAdapter` class, with the following contents:
  ```php
  <?php
  
  declare(strict_types=1);
  
  namespace App\Service\Security;
  
  use Laminas\Authentication\Adapter\Http\ResolverInterface;
  use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
  use Laminas\Authentication\Result as AuthResult;
  
  final class AuthAdapter implements ValidatableAdapterInterface
  {
      private mixed $identity;
      private mixed $credentials;
      private ResolverInterface $resolver;
  
      public function __construct(ResolverInterface $resolver)
      {
          $this->resolver = $resolver;
      }
  
      /**
       * @inheritDoc
       */
      public function authenticate(): AuthResult
      {
          /** @var AuthResult $result */
          $result = $this->resolver
              ->resolve(
                  $this->identity,
                  '',
                  $this->credentials,
              );
  
          return $result;
      }
  
      /**
       * @inheritDoc
       */
      public function getIdentity()
      {
          return $this->identity;
      }
  
      /**
       * @inheritDoc
       */
      public function setIdentity($identity)
      {
          $this->identity = $identity;
  
          return $this;
      }
  
      /**
       * @inheritDoc
       */
      public function getCredential()
      {
          return $this->credentials;
      }
  
      /**
       * @inheritDoc
       */
      public function setCredential($credential)
      {
          $this->credentials = $credential;
  
          return $this;
      }
  }
  ```
* `AuthServiceInterface` interface, with the following contents:
  ```php
  <?php

  declare(strict_types=1);

  namespace App\Service\Security;

  use Laminas\Authentication\Result;

  interface AuthServiceInterface
  {
      public function authenticate(string $username, ?string $password): Result;

      public function isSignedIn(): bool;

      public function signOut(): void;

      public function getIdentity(): ?string;
  }
  ```
* `AuthService` class, with the following contents:
  ```php
  <?php
  
  declare(strict_types=1);
  
  namespace App\Service\Security;
  
  use Laminas\Authentication\AuthenticationService as ActualAuthenticationService;
  use Laminas\Authentication\Result;
  
  final class AuthService implements AuthServiceInterface
  {
      private ActualAuthenticationService $actualAuthService;
  
      public function __construct(ActualAuthenticationService $authService)
      {
          $this->actualAuthService = $authService;
      }
  
      public function authenticate(string $username, ?string $password): Result
      {
          /** @var AuthAdapter $adapter */
          $adapter = $this->actualAuthService
              ->getAdapter();
  
          return $this->actualAuthService
              ->authenticate(
                  $adapter
                      ->setIdentity($username)
                      ->setCredential($password)
              );
      }
  
      public function isSignedIn(): bool
      {
          return $this->actualAuthService
              ->hasIdentity();
      }
  
      public function signOut(): void
      {
          $this->actualAuthService
              ->clearIdentity();
      }
  
      public function getIdentity(): ?string
      {
          return $this->actualAuthService
              ->getIdentity();
      }
  }
  ```

Create a new service provider, called `SecurityProvider`, in your application's `src/Provider` directory, with the
following contents:

```php
<?php

declare(strict_types=1);

namespace App\Provider;

use App\Service\Security\AuthAdapter;
use App\Service\Security\AuthService;
use App\Service\Security\AuthServiceInterface;
use Laminas\Authentication\Adapter\AdapterInterface as AuthAdapterInterface;
use Laminas\Authentication\Adapter\Http\ApacheResolver;
use Laminas\Authentication\Adapter\Http\ResolverInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Authentication\Storage\Session;
use Laminas\Authentication\Storage\StorageInterface;
use Noctis\KickStart\Configuration\Configuration;
use Noctis\KickStart\Provider\ServicesProviderInterface;
use Noctis\KickStart\Service\Container\Definition\Autowire;
use Noctis\KickStart\Service\Container\Definition\Reference;

use function Noctis\KickStart\Service\Container\autowire;
use function Noctis\KickStart\Service\Container\reference;

final class SecurityProvider implements ServicesProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getServicesDefinitions(): array
    {
        return [
            AuthAdapterInterface::class => AuthAdapter::class,
            AuthenticationService::class => autowire(AuthenticationService::class)
                ->constructorParameter(
                    'storage',
                     reference(StorageInterface::class),
                )
                ->constructorParameter(
                    'adapter',
                    reference(AuthAdapterInterface::class)
                ),
            AuthenticationServiceInterface::class => AuthenticationService::class,
            AuthServiceInterface::class => AuthService::class,
            ResolverInterface::class => function (): ApacheResolver {
                return new ApacheResolver(
                    Configuration::get('security.htpasswd_path')
                );
            },
            StorageInterface::class => autowire(Session::class)
                ->constructorParameter(
                    'namespace',
                    Configuration::get('security.realm')
                ),
        ];
    }
}
```

Register the new service provider in `public/index.php` file:

```php
<?php

declare(strict_types=1);

use App\Provider\SecurityProvider;
use Noctis\KickStart\Http\Routing\RouteInterface;
use Noctis\KickStart\Http\WebApplication;

// ...

$app = WebApplication::boot(
    // ... ,
    new SecurityProvider()
);

/** @var list<RouteInterface> $routes */
$routes = require_once __DIR__ . '/../src/Http/Routing/routes.php';
$app->setRoutes($routes);
$app->run();
```

There is no need to register this new service provider in `bin/console`.

## Configuration

Add the following lines to both `.env` and `.env-example` files:

```dotenv
# Security
security.htpasswd_path="/full/path/to/.htpasswd"
# A unique value (may contain alphanumerics, backslashes and underscores only!)
security.realm="kickstart"
```

The value for `security.htpasswd_path` in `.env` should be a full path to a `.htpasswd` file, where all the usernames
and passwords will be stored.

**IMPORTANT:** NEVER place files with such sensitive information in a web-accessible location, such as the `public`
folder! Usually your Web server will deny public access to any files which name's starts with a dot, but it's better to
be safe than sorry.

**IMPORTANT:** If you decide to keep your `.htpasswd` file in your application's directory, make sure to add it to
`.gitignore`. You *don't* want to commit sensitive information to your repository!

Define the two new configuration options as required, by modifying your the `bootstrap.php` file, in your application's
root directory:

```php
<?php

declare(strict_types=1);

use Dotenv\Dotenv;
// ...

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
$dotenv->required([
    // ...
    'security.htpasswd_path',
    'security.realm',
])
->notEmpty();

// ...
```

## HTTP Actions

Create an HTTP action class for the sign-in form, called `SignInFormAction`, in your application's `src/Http/Action` 
directory:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Laminas\Diactoros\Response\HtmlResponse;
use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Helper\RenderTrait;
use Noctis\KickStart\Http\Response\Factory\HtmlResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SignInFormAction implements ActionInterface
{
    use RenderTrait;

    public function __construct(HtmlResponseFactoryInterface $htmlResponseFactory)
    {
        $this->htmlResponseFactory = $htmlResponseFactory;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): HtmlResponse
    {
        return $this->render('sign-in-form.html.twig');
    }
}
```

Define a route for the action you've just created, in the `src/Http/Routing/routes.php` file:

```php
<?php

declare(strict_types=1);

// ...
use App\Http\Action\SignInFormAction;
use Noctis\KickStart\Http\Routing\Route;

return [
    // ...
    Route::get('/sign-in', SignInFormAction::class),
];
```

Now, create a file named `sign-in-form.html.twig` in the application's `templates` directory, with a simple sign-in
form, for example:

```html
{% extends "layout.html.twig" %}

{% block content %}
<div class="container">
    <form method="post">
        <div class="row justify-content-center">
            <div class="col-4 mt-5">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="u">User:</label>
                            <input type="text" class="form-control" id="u" name="u">
                        </div>
                        <div class="form-group">
                            <label for="p">Password:</label>
                            <input type="password" class="form-control" id="p" name="p">
                        </div>
            
                        <button type="submit" class="btn btn-success">Sign In</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
{% endblock %}
```

If you open your application now in a Web browser and go to the `/sign-in` URL, you should see the sign-in form you've
just created. Now let's create a separate HTTP action, for handling this form's submissions.

Create a new HTTP action called `SignInAction`, in your application's `src/Http/Action` directory, with the following 
contents:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Service\Security\AuthServiceInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Helper\RedirectTrait;
use Noctis\KickStart\Http\Response\Factory\RedirectResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SignInAction implements ActionInterface
{
    use RedirectTrait;

    private AuthServiceInterface $authService;

    public function __construct(
        AuthServiceInterface $authService,
        RedirectResponseFactoryInterface $redirectResponseFactory
    ) {
        $this->authService = $authService;
        $this->redirectResponseFactory = $redirectResponseFactory;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): RedirectResponse
    {
        /** @var array $body */
        $body = $request->getParsedBody();
        /** @var string $username */
        $username = $body['u'];
        /** @var string $password */
        $password = $body['p'];

        $authResult = $this->authService
            ->authenticate($username, $password);

        if (!$authResult->isValid()) {
            return $this->redirect('sign-in');
        }

        // There's no route for this URL, it's just for demonstration purposes
        return $this->redirect('hello');
    }
}
```

Define a new route for this action, in the `src/Http/Routing/routes.php` file:

```php
<?php

declare(strict_types=1);

// ...
use App\Http\Action\SignInAction;
use App\Http\Action\SignInFormAction;
use Noctis\KickStart\Http\Routing\Route;

return [
    // ...
    Route::get('/sign-in', SignInFormAction::class),
    Route::post('/sign-in', SignInAction::class),
];
```

If valid credentials are provided in the sign-in form, one will be redirected to the `/hello` URL. Otherwise, one will
be redirected back to the sign-in form.

## Sign Out

Signing-in works now, but there's still the matter of being able to sign-out. Create an HTTP action class named 
`SignOutAction`, in your application's `src/Http/Action` directory, with the following contents:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Service\Security\AuthServiceInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Helper\RedirectTrait;
use Noctis\KickStart\Http\Response\Factory\RedirectResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SignOutAction implements ActionInterface
{
    use RedirectTrait;

    private AuthServiceInterface $authService;

    public function __construct(
        AuthServiceInterface $authService,
        RedirectResponseFactoryInterface $redirectResponseFactory
    ) {
        $this->authService = $authService;
        $this->redirectResponseFactory = $redirectResponseFactory;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): RedirectResponse
    {
        $this->authService
            ->signOut();

        return $this->redirect('/');
    }
}
```

Add it to the routes list, in the `src/Http/Routing/routes.php` file:

```php
<?php

declare(strict_types=1);

// ...
use App\Http\Action\SignInAction;
use App\Http\Action\SignInFormAction;
use App\Http\Action\SignOutAction;
use Noctis\KickStart\Http\Routing\Route;

return [
    // ...
    Route::get('/sign-in', SignInFormAction::class),
    Route::post('/sign-in', SignInAction::class),
    Route::get('/sign-out', SignOutAction::class),
];
```

Requesting the `/sign-out` URL in the browser will now cause one to be signed-out and redirected to the `/` URL.

## Authorization Middleware

Lastly, you should create some basic authorization mechanism, which:

* lets only signed-in users access the secure sections of your website, and
* redirects everyone else, i.e. anonymous (not signed-in) users to the sign-in form.

The most convenient way to do this is to create a middleware, which will act as such a guard and assign it to any action
which is considered secure.

Create a class called `IsLoggedInGuard` in your application's `src/Http/Middleware/Guard` directory, with the following
contents:

```php
<?php

declare(strict_types=1);

namespace App\Http\Middleware\Guard;

use App\Service\Security\AuthServiceInterface;
use Noctis\KickStart\Http\Helper\RedirectTrait;
use Noctis\KickStart\Http\Response\Factory\RedirectResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class IsLoggedInGuard implements MiddlewareInterface
{
    use RedirectTrait;

    private AuthServiceInterface $authService;

    public function __construct(
        AuthServiceInterface $authService,
        RedirectResponseFactoryInterface $redirectResponseFactory
    ) {
        $this->authService = $authService;
        $this->redirectResponseFactory = $redirectResponseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->authService->isSignedIn()) {
            return $this->redirect('sign-in');
        }

        return $handler->handle($request);
    }
}
```

Next, assign it to any actions only signed-in uses should have access to, by modifying their routes, in 
`src/Http/Routing/routes.php` file, for example:

```php
<?php

declare(strict_types=1);

// ...
use App\Http\Action\HelloAction;
use App\Http\Action\SignInAction;
use App\Http\Action\SignInFormAction;
use App\Http\Action\SignOutAction;
use App\Http\Middleware\Guard\IsLoggedInGuard;
use Noctis\KickStart\Http\Routing\Route;

return [
    Route::get('/hello', HelloAction::class, [IsLoggedInGuard::class]),
    // ...
    Route::get('/sign-in', SignInFormAction::class),
    Route::post('/sign-in', SignInAction::class),
    Route::get('/sign-out', SignOutAction::class),
];
```

**IMPORTANT:** NEVER assign this middleware to the sign-in form (here: `SignInFormAction`), sign-in handling (here: 
`SignInAction`) and sign-out (here: `SignOutAction`) actions! Those three actions should **always** be available to any
user, signed-in or not. They know how to handle them.

## Can't Sign In?

If you did everything according to this instruction, you should now be able to go to `/sign-in` URL in your browser,
where you will see a sign-in form. If you enter invalid or non-existent credentials and submit the form, you will be
redirected back to the form. If the credentials you've entered are correct, you should be redirected to the `/hello`
URL.

If you keep being redirected back to the sign-in form, despite entering valid credentials, check the 
`security.htpasswd_path` option value in your application's `.env` file; make sure the path to the `.htpasswd` file 
defined there is valid, the file exists and is readable. Remember: **this needs to be a full path to the
file, not just the folder where it resides!**

If the file does exist and is readable, make sure the username you're using is in fact defined in that file. Also, make
sure no two lines are "glued" together, i.e. lines for two different users are both on the same line, for example:

```
admin:$2y$10$W5B8FD1NfVKM0d4e4p6Vwu5nsTxhI4UhyI7Y/gxAenbvBtPb33BTyguest:$2y$10$VQpsX8AlS7au7Pl5RDPBC.FJW2kAm4N8g4I7leswdpnfm.6z2pd2e
```

In this example, lines for both `admin` and `guest` users have been "glued" together. Each user should be on their own,
separate line, like so:

```dotenv
admin:$2y$10$W5B8FD1NfVKM0d4e4p6Vwu5nsTxhI4UhyI7Y/gxAenbvBtPb33BTy
guest:$2y$10$VQpsX8AlS7au7Pl5RDPBC.FJW2kAm4N8g4I7leswdpnfm.6z2pd2e
```