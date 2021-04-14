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
`Noctis\KickStart\Repository\AbstractDatabaseRepository` abstract class and implementing the `PostRepositoryInterface`
interface:

```php
<?php

declare(strict_types=1);

namespace App\Repository;

use Noctis\KickStart\Repository\AbstractDatabaseRepository;

final class PostRepository extends AbstractDatabaseRepository implements PostRepositoryInterface
{
    public function find(int $id): array
    {
        // ...
    }
}
```

Last thing you should do is to register the new repository (and its interface) in the Dependency Injection Container.
To do that, edit the `src/Provider/RepositoryProvider.php` file and add the following line to the list:

```php
App\Repository\PostRepositoryInterface::class => App\Repository\PostRepository::class,
```
