<?php

declare(strict_types=1);

namespace App\Http\Middleware\Guard;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DummyGuard implements MiddlewareInterface
{
    public function __construct(private bool $dummyParam)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // inspect $request, do stuff, maybe return a RedirectResponse instance?

        return $handler->handle($request);
    }
}
