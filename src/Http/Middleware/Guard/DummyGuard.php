<?php

declare(strict_types=1);

namespace App\Http\Middleware\Guard;

use Noctis\KickStart\Http\Middleware\AbstractMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DummyGuard extends AbstractMiddleware
{
    private bool $dummyParam;

    public function __construct(bool $dummyParam)
    {
        $this->dummyParam = $dummyParam;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // inspect $request, do stuff, maybe return a RedirectResponse instance?

        return parent::process($request, $handler);
    }
}
