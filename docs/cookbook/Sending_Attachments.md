# Sending Files in Response

If you wish to execute an [HTTP action](../HTTP.md) and send an attachment (a file) in response, for the Web browser to 
download, you can use the `sendAttachment()` method available in every HTTP action class. This method needs to be
provided an instance of the `Noctis\KickStart\Http\Response\Attachment\Attachment` class.

An instance of the aforementioned class can be created from, either: 

* an existing file,
* a string,
* a file resource.

Kickstart offers an attachment factory class, called 
`Noctis\KickStart\Http\Response\Attachment\AttachmentFactoryInterface`.

## Sending an Existing File as an Attachment

To create an `Attachment` object, representing an existing (on-disk) file, you need to call the `createFromPath()` 
method of the `AttachmentFactory`, passing 3 parameters:

* an absolute path to the file you wish to send,
* the MIME type declaration (will be sent to the browser),
* an `Noctis\KickStart\Http\Response\Headers\Disposition` object, representing the 
  [`Content-Disposition`](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Disposition) HTTP header, 
  containing the filename that will be sent to the browser. 

Here's an example of an HTTP action, which sends the local `/tmp/result.png` file, under the `result.png` name:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Noctis\KickStart\Http\Action\AbstractAction;
use Noctis\KickStart\Http\Response\Attachment\AttachmentFactoryInterface;
use Noctis\KickStart\Http\Response\AttachmentResponse;
use Noctis\KickStart\Http\Response\Headers\Disposition;

final class SendFileAction extends AbstractAction
{
    public function execute(AttachmentFactoryInterface $attachmentFactory): AttachmentResponse
    {
        return $this->sendAttachment(
            $attachmentFactory->createFromPath(
                '/tmp/result.png',
                'image/png',
                new Disposition('result.png')
            )
        );
    }
}
```

## Sending a String as an Attachment

Suppose you want to send a string to the browser, as an attachment. In such case, you should use the 
`createFromContent()` method of `AttachmentFactory`. This method takes 3 parameters:

* the string (content) you wish to send,
* the MIME type declaration (will be sent to the browser),
* an `Noctis\KickStart\Http\Response\Headers\Disposition` object, representing the
  [`Content-Disposition`](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Disposition) HTTP header,
  containing the filename that will be sent to the browser.

Here's an example of an HTTP action, which sends a string, as a `output.csv` file:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Noctis\KickStart\Http\Action\AbstractAction;
use Noctis\KickStart\Http\Response\Attachment\AttachmentFactoryInterface;
use Noctis\KickStart\Http\Response\AttachmentResponse;
use Noctis\KickStart\Http\Response\Headers\Disposition;

final class SendFileAction extends AbstractAction
{
    public function execute(AttachmentFactoryInterface $attachmentFactory): AttachmentResponse
    {
        $content = 'foo,bar,baz';

        return $this->sendAttachment(
            $attachmentFactory->createFromContent(
                $content,
                'text/csv; charset=UTF-8',
                new Disposition('output.csv')
            )
        );
    }
}
```

## Sending a File Resource as an Attachment

Sometimes, instead of file on the disk, or a string, you might have a resource on your hand.

Here's an example of an HTTP action, which sends a temporary file resource (`php://temp`) to the browser, under the 
`output.csv` name:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Noctis\KickStart\Http\Action\AbstractAction;
use Noctis\KickStart\Http\Response\Attachment\AttachmentFactoryInterface;
use Noctis\KickStart\Http\Response\AttachmentResponse;
use Noctis\KickStart\Http\Response\Headers\Disposition;

final class SendFileAction extends AbstractAction
{
    public function execute(AttachmentFactoryInterface $attachmentFactory): AttachmentResponse
    {
        $resource = fopen('php://temp', 'w+');
        fwrite($resource, 'foo,bar,baz');
        rewind($resource);

        return $this->sendAttachment(
            $attachmentFactory->createFromResource(
                $resource,
                'text/csv; charset=UTF-8',
                new Disposition('output.csv')
            )
        );
    }
}
```
