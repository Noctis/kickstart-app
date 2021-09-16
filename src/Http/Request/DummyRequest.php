<?php

declare(strict_types=1);

namespace App\Http\Request;

use App\Service\DummyServiceInterface;
use Laminas\Session\ManagerInterface;
use Noctis\KickStart\Http\Request\Request;
use Psr\Http\Message\ServerRequestInterface;

final class DummyRequest extends Request
{
    private DummyServiceInterface $dummyService;

    public function __construct(
        ServerRequestInterface $request,
        ManagerInterface $sessionManager,
        DummyServiceInterface $dummyService
    ) {
        parent::__construct($request, $sessionManager);

        $this->dummyService = $dummyService;
    }

    public function getFoo(): string
    {
        return $this->dummyService
            ->foo();
    }
}
