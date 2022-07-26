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

    public function __construct(HtmlResponseFactoryInterface $htmlResponseFactory)
    {
        $this->htmlResponseFactory = $htmlResponseFactory;
    }

    /**
     * @param DummyRequest $request
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): HtmlResponse
    {
        /** @var string $name */
        $name = $request->getQueryParams()['name'] ?? 'World';

        return $this->render('dummy.html.twig', [
            'name' => $name,
            'foo'  => $request->getFoo()
        ]);
    }
}
