<?php

declare(strict_types=1);

namespace App\Http\Action;

use Laminas\Diactoros\Response\HtmlResponse;
use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Helper\RequestHelperInterface;
use Noctis\KickStart\Http\Response\Factory\HtmlResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DummyAction implements ActionInterface
{
    private RequestHelperInterface $requestHelper;
    private HtmlResponseFactoryInterface $htmlResponseFactory;

    public function __construct(
        RequestHelperInterface $requestHelper,
        HtmlResponseFactoryInterface $htmlResponseFactory
    ) {
        $this->requestHelper = $requestHelper;
        $this->htmlResponseFactory = $htmlResponseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): HtmlResponse
    {
        /** @var string $name */
        $name = $this->requestHelper
            ->get($request, 'name') ?: 'World';

        return $this->htmlResponseFactory
            ->render('dummy.html.twig', [
                'name' => $name,
                'foo'  => $this->requestHelper
                    ->get($request, 'foo'),
            ]);
    }
}
