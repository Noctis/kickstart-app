<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Http\Request\DummyRequest;
use Laminas\Diactoros\Response\HtmlResponse;
use Noctis\KickStart\Http\Action\ActionInterface;
use Noctis\KickStart\Http\Helper\RenderTrait;
use Noctis\KickStart\Http\Response\Factory\HtmlResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DummyAction implements ActionInterface
{
    use RenderTrait;

    private DummyRequest $request;

    public function __construct(DummyRequest $request, HtmlResponseFactoryInterface $htmlResponseFactory)
    {
        $this->request = $request;
        $this->htmlResponseFactory = $htmlResponseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): HtmlResponse
    {
        $name = $this->request
            ->getQueryParams()['name'] ?? 'World';

        return $this->render('dummy.html.twig', [
            'name' => $name,
            'foo'  => $this->request
                ->getFoo()
        ]);
    }
}
