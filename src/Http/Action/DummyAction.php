<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Http\Request\DummyRequest;
use Laminas\Diactoros\Response\HtmlResponse;
use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Response\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DummyAction implements ActionInterface
{
    private DummyRequest $request;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(DummyRequest $request, ResponseFactoryInterface $responseFactory)
    {
        $this->request = $request;
        $this->responseFactory = $responseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): HtmlResponse
    {
        /** @var string $name */
        $name = $this->request
            ->get('name') ?: 'World';

        /** @psalm-suppress DeprecatedMethod */
        return $this->responseFactory
            ->htmlResponse('dummy.html.twig', [
                'name' => $name,
                'foo'  => $this->request
                    ->getFoo(),
            ]);
    }
}
