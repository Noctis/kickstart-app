<?php

declare(strict_types=1);

namespace App\Http\Middleware\Guard;

use Noctis\KickStart\Configuration\ConfigurationInterface;
use Noctis\KickStart\Http\Helper\HttpRedirectionTrait;
use Noctis\KickStart\Http\Middleware\AbstractMiddleware;
use Noctis\KickStart\Http\Middleware\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @psalm-suppress PropertyNotSetInConstructor */
final class DummyGuard extends AbstractMiddleware
{
    use HttpRedirectionTrait;

    private ConfigurationInterface $configuration;
    private bool $dummyParam;

    public function __construct(ConfigurationInterface $configuration, bool $dummyParam)
    {
        $this->configuration = $configuration;
        $this->dummyParam = $dummyParam;
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        // inspect $request, do stuff, maybe return a RedirectResponse instance, via $this->redirect()?

        return parent::process($request, $handler);
    }
}
