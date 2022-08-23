<?php

declare(strict_types=1);

namespace App\Provider;

use App\Http\Middleware\Guard\DummyGuard;
use Noctis\KickStart\Provider\ServicesProviderInterface;

final class HttpMiddlewareProvider implements ServicesProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getServicesDefinitions(): array
    {
        return [
            DummyGuard::class => ['dummyParam' => true],
        ];
    }
}
