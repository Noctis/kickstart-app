# Sending Files in Response

If you wish to run an [HTTP action](../HTTP.md) and send a file (attachment) in response, for the Web browser to download,
you can use the `sendFile()` method available in the action class. But, how you do get an instance of
`Noctis\KickStart\File\FileInterface` that the method asks for?

You have two options:
* use the `Noctis\KickStart\File\File` class, provided by Kickstart, or
* create a custom file class, extending the aforementioned `File` class.

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
