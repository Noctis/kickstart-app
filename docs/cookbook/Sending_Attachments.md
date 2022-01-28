# Sending Files in Response

If you wish to execute an [HTTP action](../HTTP.md) and send an attachment (a file) in response, for the Web browser to 
download, that action's `process()` method needs to return an instance of 
`Noctis\KickStart\Http\Response\AttachmentResponse` class.

To create an attachment response object, include the `Noctis\KickStart\Http\Helper\AttachmentTrait` trait into your 
action and make sure an instance of the `Noctis\KickStart\Http\Response\Factory\AttachmentResponseFactory` is injected 
into the local `$attachmentResponseFactory` field:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Helper\AttachmentTrait;
use Noctis\KickStart\Http\Response\Factory\AttachmentResponseFactoryInterface;

final class DummyAction implements ActionInterface
{
    use AttachmentTrait;

    public function __construct(AttachmentResponseFactoryInterface $attachmentResponseFactory)
    {
        $this->attachmentResponseFactory = $attachmentResponseFactory;
    }
    
    // ...
}
```

From now on, the action class has access to a couple of methods, all of which return an `AttachmentResponse` object. The
difference between them is what they create that response object from:

* `sendAttachment()` - creates a response object from an instance of 
  `Noctis\KickStart\Http\Response\Attachment\AttachmentInterface`,
* `sendFile()` - creates a response object from an existing, on-disk file,
* `sendContent()` - creates a response object from an in-memory string,
* `sendResource()` - creates a response object from a 
  [PHP resource](https://www.php.net/manual/en/language.types.resource.php).

## Sending an Instance of `AttachmentInterface`

To send an instance of `AttachmentInterface` in your response, simply pass it to the `AttachmentTrait::sendAttachment()`
method:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Helper\AttachmentTrait;
use Noctis\KickStart\Http\Response\AttachmentResponse;
use Noctis\KickStart\Http\Response\Factory\AttachmentResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SendAttachmentAction implements ActionInterface
{
    use AttachmentTrait;

    public function __construct(AttachmentResponseFactoryInterface $attachmentResponseFactory)
    {
        $this->attachmentResponseFactory = $attachmentResponseFactory;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): AttachmentResponse
    {
        // ...
    
        return $this->sendAttachment($attachment);
    }
}
```

## Sending an Existing File as an Attachment

To return an existing (on-disk) file as an attachment, you need to call the `AttachmentTrait::sendFile()` method, 
passing three parameters:

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
use Noctis\KickStart\Http\Helper\AttachmentTrait;
use Noctis\KickStart\Http\Response\AttachmentResponse;
use Noctis\KickStart\Http\Response\Factory\AttachmentResponseFactoryInterface;
use Noctis\KickStart\Http\Response\Headers\Disposition;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SendFileAction implements ActionInterface
{
    use AttachmentTrait;

    public function __construct(AttachmentResponseFactoryInterface $attachmentResponseFactory)
    {
        $this->attachmentResponseFactory = $attachmentResponseFactory;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): AttachmentResponse
    {
        return $this->sendFile(
            '/tmp/result.png',
            'image/png',
            Disposition::attachment('result.png')
        );
    }
}
```

## Sending a String as an Attachment

Suppose you want to send a string to the browser, as an attachment. In such case, you should use the 
`AttachmentTrait::sendContent()` method. This method takes three parameters:

* the string (content) you wish to send,
* the MIME type declaration (will be sent to the browser),
* an `Noctis\KickStart\Http\Response\Headers\Disposition` object, representing the
  [`Content-Disposition`](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Disposition) HTTP header,
  containing the filename that will be sent to the browser.

Here's an example of an HTTP action, which sends a string as a `output.csv` file:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Helper\AttachmentTrait;
use Noctis\KickStart\Http\Response\AttachmentResponse;
use Noctis\KickStart\Http\Response\Factory\AttachmentResponseFactoryInterface;
use Noctis\KickStart\Http\Response\Headers\Disposition;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SendContentAction implements ActionInterface
{
    use AttachmentTrait;

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

        return $this->sendContent(
            $content,
            'text/csv; charset=UTF-8',
            Disposition::attachment('output.csv')
        );
    }
}
```

## Sending a PHP Resource as an Attachment

Sometimes, instead of file on the disk, or a string, you might have a 
[PHP resource](https://www.php.net/manual/en/language.types.resource.php) on your hand. In such case, you should use the
`AttachmentTrait::sendResource()` method. This method takes three parameters:

* the PHP resource you wish to send,
* the MIME type declaration (will be sent to the browser),
* an `Noctis\KickStart\Http\Response\Headers\Disposition` object, representing the
  [`Content-Disposition`](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Disposition) HTTP header,
  containing the filename that will be sent to the browser.

Here's an example of an HTTP action, which sends a temporary file resource (`php://temp`) to the browser, under the 
`output.csv` name:

```php
<?php

declare(strict_types=1);

namespace App\Http\Action;

use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Helper\AttachmentTrait;
use Noctis\KickStart\Http\Response\AttachmentResponse;
use Noctis\KickStart\Http\Response\Factory\AttachmentResponseFactoryInterface;
use Noctis\KickStart\Http\Response\Headers\Disposition;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SendResourceAction implements ActionInterface
{
    use AttachmentTrait;

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

        return $this->sendResource(
            $resource,
            'text/csv; charset=UTF-8',
            Disposition::attachment('output.csv')
        );
    }
}
```
