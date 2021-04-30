# Sending Files in Response

If you wish to run an [HTTP action](../HTTP.md) and send a file (attachment) in response, for the Web browser to download,
you can use the `sendFile()` method available in the action class. But, how you do get an instance of
`Noctis\KickStart\File\FileInterface` that the method asks for?

You have two options:
* use the `Noctis\KickStart\File\File` class, provided by Kickstart, or
* create a custom file class, extending the aforementioned `File` class.

## Sending an Existing File

Here's an example of a custom file class, for PNG files:

```php
<?php

declare(strict_types=1);

namespace App\File;

use Noctis\KickStart\File\File;

final class PngFile extends File
{
    public function __construct(string $filePath)
    {
        parent::__construct($filePath, 'image/png');
    }
}
```

And here's an example of how you could use it in your HTTP action:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\File\PngFile;
use Noctis\KickStart\Http\Action\AbstractAction;
use Noctis\KickStart\Http\Response\FileResponse;

final class SendFileAction extends AbstractAction
{
    public function execute(): FileResponse
    {
        return $this->sendFile(
            new PngFile($_ENV['basepath'] . '/var/files/family-picture.png')
        );
    }
}
```

## Sending an In-Memory File

Suppose you want to send an in-memory file to the browser, i.e. a file which does not exist physically on the disk (or
whatever storage device you use). In such case, you should use the `Noctis\KickStart\File\InMemoryFile`.

Here's an example of a custom file class, for an in-memory CSV file:

```php
<?php

declare(strict_types=1);

namespace App\File;

use Noctis\KickStart\File\FileInterface;
use Noctis\KickStart\File\InMemoryFile;

final class CsvFile extends InMemoryFile implements FileInterface
{
    public function __construct(string $fileName, string $csv)
    {
        parent::__construct($fileName, $csv, 'text/csv; charset=UTF-8');
    }
}
```

An instance of such a class can be passed to the HTTP action's `sendFile()` method.
