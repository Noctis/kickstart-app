<?php

declare(strict_types=1);

use App\Http\Application;
use App\Provider\ConfigurationProvider;
use App\Provider\DatabaseConnectionProvider;
use App\Provider\DummyServicesProvider;
use App\Provider\HttpMiddlewareProvider;
use App\Provider\RepositoryProvider;
use Noctis\KickStart\Http\ContainerBuilder;

require_once __DIR__ . '/../bootstrap.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder
    ->registerServicesProvider(new ConfigurationProvider())
    ->registerServicesProvider(new DatabaseConnectionProvider())
    ->registerServicesProvider(new HttpMiddlewareProvider())
    ->registerServicesProvider(new DummyServicesProvider())
    ->registerServicesProvider(new RepositoryProvider())
;
$container = $containerBuilder->build();

/** @var Application $app */
$app = $container->get(Application::class);
$app->setRoutes(
    require_once __DIR__ . '/../src/Http/Routing/routes.php'
);
$app->run();
