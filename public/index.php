<?php

declare(strict_types=1);

use App\Provider\ConfigurationProvider;
use App\Provider\DatabaseConnectionProvider;
use App\Provider\DummyServicesProvider;
use App\Provider\HttpMiddlewareProvider;
use App\Provider\RepositoryProvider;
use Noctis\KickStart\Http\ContainerBuilder;
use Noctis\KickStart\Http\WebApplication;

require_once __DIR__ . '/../bootstrap.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder
    ->registerServicesProvider(new ConfigurationProvider())
    ->registerServicesProvider(new DatabaseConnectionProvider())
    ->registerServicesProvider(new HttpMiddlewareProvider())
    ->registerServicesProvider(new DummyServicesProvider())
    ->registerServicesProvider(new RepositoryProvider())
    ->set(
        'routes',
        require_once __DIR__ . '/../src/Http/Routing/routes.php'
    )
;
$container = $containerBuilder->build();

/** @var WebApplication $app */
$app = $container->get(WebApplication::class);
$app->run();
