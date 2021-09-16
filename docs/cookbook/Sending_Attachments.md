# Sending Files in Response

If you wish to execute an [HTTP action](../HTTP.md) and send an attachment (a file) in response, for the Web browser to 
download, that action's `proccess()` method needs to return an instance of 
`Noctis\KickStart\Http\Response\AttachmentResponse` class. You can create such an object using one of the methods
available in the `Noctis\KickStart\Http\Response\Factory\AttachmentResponseFactory` class. 

An `AttachmentResponse` can be created using one of the following methods:

* `sendFile()` - from an existing, on-disk file,
* `sendContent()` - from an in-memory string,
* `sendResource()` - from a [PHP resource](https://www.php.net/manual/en/language.types.resource.php). 

## Sending an Existing File as an Attachment

To return an existing (on-disk) file as an attachment, you need to call the `sendFile()` method of the 
`AttachmentResponseFactory`, passing 3 parameters:

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

use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Response\AttachmentResponse;
use Noctis\KickStart\Http\Response\Factory\AttachmentResponseFactoryInterface;
use Noctis\KickStart\Http\Response\Headers\Disposition;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SendFileAction implements ActionInterface
{
    private AttachmentResponseFactoryInterface $attachmentResponseFactory;

    public function __construct(AttachmentResponseFactoryInterface $attachmentResponseFactory) 
    {
        $this->attachmentResponseFactory = $attachmentResponseFactory;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): AttachmentResponse
    {
        return $this->attachmentResponseFactory
            ->sendFile(
                '/tmp/result.png',
                'image/png',
                new Disposition('result.png')
            );
    }
}
```

## Sending a String as an Attachment

Suppose you want to send a string to the browser, as an attachment. In such case, you should use the 
`sendContent()` method of `AttachmentResponseFactory`. This method takes 3 parameters:

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

use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Response\AttachmentResponse;
use Noctis\KickStart\Http\Response\Factory\AttachmentResponseFactoryInterface;
use Noctis\KickStart\Http\Response\Headers\Disposition;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SendFileAction implements ActionInterface
{
    private AttachmentResponseFactoryInterface $attachmentResponseFactory;

    public function __construct(AttachmentResponseFactoryInterface $attachmentResponseFactory)
    {
        $this->attachmentResponseFactory = $attachmentResponseFactory;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): AttachmentResponse
    {
        $content = 'foo,bar,baz';

        return $this->attachmentResponseFactory
            ->sendContent(
                $content,
                'text/csv; charset=UTF-8',
                new Disposition('output.csv')
            );
    }
}
```

## Sending a PHP Resource as an Attachment

Sometimes, instead of file on the disk, or a string, you might have a 
[PHP resource](https://www.php.net/manual/en/language.types.resource.php) on your hand.

Here's an example of an HTTP action, which sends a temporary file resource (`php://temp`) to the browser, under the 
`output.csv` name:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Response\AttachmentResponse;
use Noctis\KickStart\Http\Response\Factory\AttachmentResponseFactoryInterface;
use Noctis\KickStart\Http\Response\Headers\Disposition;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SendFileAction implements ActionInterface
{
    private AttachmentResponseFactoryInterface $attachmentResponseFactory;

    public function __construct(AttachmentResponseFactoryInterface $attachmentResponseFactory)
    {
        $this->attachmentResponseFactory = $attachmentResponseFactory;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): AttachmentResponse
    {
        $resource = fopen('php://temp', 'w+');
        fwrite($resource, 'foo,bar,baz');
        rewind($resource);

        return $this->attachmentResponseFactory
            ->sendResource(
                $resource,
                'text/csv; charset=UTF-8',
                new Disposition('output.csv')
            );
    }
}
```
