<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Http\Request\DummyRequest;
use Noctis\KickStart\Http\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Response;

final class DummyAction extends AbstractAction
{
    public function execute(DummyRequest $request): Response
    {
        /** @var string $name */
        $name = $request->get('name') ?: 'World';

        return $this->render('dummy.html.twig', [
            'name' => $name,
            'foo'  => $request->getFoo(),
        ]);
    }
}
