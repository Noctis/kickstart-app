# Adding a Second Database Connection

There are some situations where an application needs to use more than one database connection at a time. Here's how
to add a second database connection to your application, for a specific repository.

Start by adding the second database connection parameters to `.env`:

```dotenv
secondary_db_host=localhost
secondary_db_user=dbuser
secondary_db_pass=dbpass
secondary_db_name=dbname
secondary_db_port=3306
```

Now declare those 5 new parameters as required by the application, by modifying the `bootstrap.php` file:
```php
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
$dotenv->required([
    // ...
    'secondary_db_host',
    'secondary_db_user',
    'secondary_db_pass',
    'secondary_db_name',
    'secondary_db_port'
]);
```

Also, declare that the `secondary_db_port` option's value needs to be an integer (also in `bootstrap.php`):
```php
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// ...

$dotenv->required('secondary_db_port')
    ->notEmpty()
    ->isInteger();
```

Now, edit the `src/Provider/DatabaseConnectionProvider.php` file and add a factory for the new database connection to
the Dependency Injection Container:

```php
<?php

declare(strict_types=1);

namespace App\Provider;

use Noctis\KickStart\Configuration\Configuration;
use Noctis\KickStart\Provider\ServicesProviderInterface;
use ParagonIE\EasyDB\EasyDB;
use ParagonIE\EasyDB\Exception\ConstructorFailed;
use ParagonIE\EasyDB\Factory;

final class DatabaseConnectionProvider implements ServicesProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getServicesDefinitions(): array
    {
        return [
            // ...
            'secondary_db_connection' => function (): EasyDB {
                try {
                    /** @psalm-suppress MixedArgument */
                    return Factory::fromArray([
                        sprintf(
                            'mysql:dbname=%s;host=%s;port=%s',
                            Configuration::get('secondary_db_name'),
                            Configuration::get('secondary_db_host'),
                            Configuration::get('secondary_db_port')
                        ),
                        Configuration::get('secondary_db_user'),
                        Configuration::get('secondary_db_pass')
                    ]);
                } catch (ConstructorFailed $ex) {
                    die('Could not connect to secondary DB: ' . $ex->getMessage());
                }
            },
        ];
    }
}
```

Let's assume that the repository you're trying to pass the secondary database connection to is called
`App\Repository\SecondaryRepository` and it looks like so:

```php
<?php

declare(strict_types=1);

namespace App\Repository;

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

use function Noctis\KickStart\Service\Container\autowire;
use function Noctis\KickStart\Service\Container\reference;

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
                    reference('secondary_db_connection')
                ),
            // ...
        ];
    }
}
```

Now whenever an instance of `SecondaryRepositoryInterface` is requested from the DIC, an instance of `SecodaryRepository`
will be returned, with the secondary database connection inside, while all the other repositories will be utilizing the
primary (main) database connection.
