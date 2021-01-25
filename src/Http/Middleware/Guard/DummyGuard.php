<?php declare(strict_types=1);
namespace App\Http\Middleware\Guard;

use Noctis\KickStart\Http\Helper\HttpRedirectionTrait;
use Noctis\KickStart\Http\Middleware\Guard\GuardMiddlewareInterface;
use Noctis\KickStart\Http\Middleware\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @psalm-suppress PropertyNotSetInConstructor */
final class DummyGuard implements GuardMiddlewareInterface
{
    use HttpRedirectionTrait;

    private bool $dummyParam;

    public function __construct(bool $dummyParam)
    {
        $this->dummyParam = $dummyParam;
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        // inspect $request, do stuff, maybe return a RedirectResponse instance, via $this->redirect()?

        return $handler->handle($request);
    }
}