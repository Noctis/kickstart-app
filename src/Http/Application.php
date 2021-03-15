<?php

declare(strict_types=1);

namespace App\Http;

use App\Provider\ConfigurationProvider;
use App\Provider\DatabaseConnectionProvider;
use App\Provider\DummyServicesProvider;
use App\Provider\HttpMiddlewareProvider;
use App\Provider\RepositoryProvider;
use Noctis\KickStart\Http\AbstractWebApplication;

final class Application extends AbstractWebApplication
{
    protected function getServiceProviders(): array
    {
        return array_merge(
            parent::getServiceProviders(),
            [
                new ConfigurationProvider(),
                new DatabaseConnectionProvider(),
                new HttpMiddlewareProvider(),
                new DummyServicesProvider(),
                new RepositoryProvider(),
            ]
        );
    }
}
