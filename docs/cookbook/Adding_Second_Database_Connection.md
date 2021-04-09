# Adding Second Database Connection

There are some situations where an application needs to use more than one7 database connection at a time. Here's how
to add a second database connection to your application, for a specific repository.

Start by adding the second database connection parameters to `.env`:

```dotenv
secondary_db_host=localhost
secondary_db_user=dbuser
secondary_db_pass=dbpass
secondary_db_name=dbname
secondary_db_port=3306
```

Now declare those 5 new parameters are required by the application, by modifying the `bootstrap.php` file. Add the
following lines to the array within:

```php
'secondary_db_host'  => 'required',
'secondary_db_user'  => 'required',
'secondary_db_pass'  => 'required',
'secondary_db_name'  => 'required',
'secondary_db_port'  => 'required,int',
```

You could add those new options to the `src/Configuration/FancyConfiguration.php` and 
`src/Configuration/FancyConfiguration.php` files, but I'd say it's not worth the hassle.

Now, edit the `src/Provider/DatabaseConnectionProvider.php` file and add a factory for the new database connection to
the Dependency Injection Container:

```php
'secondary_db_connection' => function (ContainerInterface $container): EasyDB {
    try {
        $configuration = $container->get(FancyConfigurationInterface::class);

        return Factory::fromArray([
            sprintf(
                'mysql:dbname=%s;host=%s;port=%s',
                $configuration->get('secondary_db_name'),
                $configuration->get('secondary_db_host'),
                $configuration->get('secondary_db_port')
            ),
            $configuration->get('secondary_db_user'),
            $configuration->get('secondary_db_pass')
        ]);
    } catch (ConstructorFailed $ex) {
        die('Could not connect to secondary DB: ' . $ex->getMessage());
    }
},
```

Let's assume that the repository you're trying to pass the secondary database connection to is called
`\App\Repository\SecondaryRepository` and it looks like so:

```php
<?php

declare(strict_types=1);

namespace App\Repository;

use Noctis\KickStart\Repository\AbstractDatabaseRepository;

final class SecondaryRepository extends AbstractDatabaseRepository implements SecondaryRepositoryInterface
{
    // ...
}
```

Modify the `src/Provider/RepositoryProvider.php` file and specify that the `SecondaryRepository` repository class
should receive an instance of the secondary database connection from DIC, instead of the default one:

```php
<?php

declare(strict_types=1);

namespace App\Provider;


use App\Repository\SecondaryRepository;
use App\Repository\SecondaryRepositoryInterface;

use function DI\autowire;
use function DI\get;

final class RepositoryProvider implements ServicesProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getServicesDefinitions(): array
    {
        return [
            // ...
            SecondaryRepositoryInterface::class => autowire(SecondaryRepository::class)
                ->constructorParameter(
                    'db',
                    get('secondary_db_connection')
                ),
            // ...
        ];
    }
} 
```

Now whenever an instance of `SecondaryRepositoryInterface` is requested from the DIC, an instance of `SecodaryRepository`
will be returned, with the secondary database connection inside, while all the other repositories will be utilizing the
primary (main) database connection.
