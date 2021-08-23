# Sending Files in Response

If you wish to execute an [HTTP action](../HTTP.md) and send an attachment (a file) in response, for the Web browser to 
download, that action's `proccess()` method needs to return an instance of 
`Noctis\KickStart\Http\Response\AttachmentResponse` class. You can create such an object using the 
`attachmentResponse()` method of the `Noctis\KickStart\Http\Response\ResponseFactory` class. This method needs to be
provided an instance of the `Noctis\KickStart\Http\Response\Attachment\Attachment` class.

An instance of the `Attachment` class can be created from, either: 

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

use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Response\Attachment\AttachmentFactoryInterface;
use Noctis\KickStart\Http\Response\Headers\Disposition;
use Noctis\KickStart\Http\Response\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SendFileAction implements ActionInterface
{
    private AttachmentFactoryInterface $attachmentFactory;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        AttachmentFactoryInterface $attachmentFactory,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->attachmentFactory = $attachmentFactory;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $attachment = $this->attachmentFactory
            ->createFromPath(
                '/tmp/result.png',
                'image/png',
                new Disposition('result.png')
            );

        return $this->responseFactory
            ->attachmentResponse($attachment);
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

use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Response\Attachment\AttachmentFactoryInterface;
use Noctis\KickStart\Http\Response\AttachmentResponse;
use Noctis\KickStart\Http\Response\Headers\Disposition;
use Noctis\KickStart\Http\Response\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SendFileAction implements ActionInterface
{
    private AttachmentFactoryInterface $attachmentFactory;
    private ResponseFactoryInterface $responseFactory;
    
    public function __construct(
        AttachmentFactoryInterface $attachmentFactory, 
        ResponseFactoryInterface $responseFactory
    ) {
        $this->attachmentFactory = $attachmentFactory;
        $this->responseFactory = $responseFactory;    
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): AttachmentResponse
    {
        $content = 'foo,bar,baz';
        $attachment = $this->attachmentFactory
            ->createFromContent(
                $content,
                'text/csv; charset=UTF-8',
                new Disposition('output.csv')
            );

        return $this->responseFactory
            ->attachmentResponse($attachment);
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

use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Response\Attachment\AttachmentFactoryInterface;
use Noctis\KickStart\Http\Response\Headers\Disposition;
use Noctis\KickStart\Http\Response\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SendFileAction implements ActionInterface
{
    private AttachmentFactoryInterface $attachmentFactory;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        AttachmentFactoryInterface $attachmentFactory,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->attachmentFactory = $attachmentFactory;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $resource = fopen('php://temp', 'w+');
        fwrite($resource, 'foo,bar,baz');
        rewind($resource);

        $attachment = $this->attachmentFactory
            ->createFromResource(
                $resource,
                'text/csv; charset=UTF-8',
                new Disposition('output.csv')
            );

        return $this->responseFactory
            ->attachmentResponse($attachment);
    }
}
```
