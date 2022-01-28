# Adding a New Database Repository

Here's what you need to do to add a new database repository.

Start by creating a new interface in the `App\Repository` namespace, called `PostRepositoryInterface`, with one method:

```php
<?php

declare(strict_types=1);

namespace App\Repository;

interface PostRepositoryInterface
{
    public function find(int $id): array;
}
```

Now create a new class called `PostRepository`, in the same namespace, extending the 
`App\Repository\AbstractDatabaseRepository` abstract class and implementing the `PostRepositoryInterface`
interface:

```php
<?php

declare(strict_types=1);

namespace App\Repository;

final class PostRepository extends AbstractDatabaseRepository implements PostRepositoryInterface
{
    public function find(int $id): array
    {
        // ...
    }
}
```

Last thing you should do is to register the new repository (and its interface) in the Dependency Injection Container.
To do that, edit the `src/Provider/RepositoryProvider.php` file and add the new dependency injection definition to the 
list:

```php
<?php

declare(strict_types=1);

namespace App\Provider;

use App\Repository\PostRepository;
use App\Repository\PostRepositoryInterface;
use Noctis\KickStart\Provider\ServicesProviderInterface;

final class RepositoryProvider implements ServicesProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getServicesDefinitions(): array
    {
        return [
            // ...
            PostRepositoryInterface::class => PostRepository::class,
        ];
    }
}
```
