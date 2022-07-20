<?php

declare(strict_types=1);

use App\Provider\DatabaseConnectionProvider;
use App\Provider\DummyServicesProvider;
use App\Provider\HttpMiddlewareProvider;
use App\Provider\RepositoryProvider;
use Noctis\KickStart\Configuration\Configuration;
use Noctis\KickStart\Http\ContainerBuilder;
use Noctis\KickStart\Http\WebApplication;
use Noctis\KickStart\Provider\RoutingProvider;

require_once __DIR__ . '/../bootstrap.php';

/** @var string */
$basePath = Configuration::get('basepath');
$containerBuilder = new ContainerBuilder();
$containerBuilder
    ->registerServicesProvider(new RoutingProvider())
    ->registerServicesProvider(new DatabaseConnectionProvider())
    ->registerServicesProvider(new HttpMiddlewareProvider())
    ->registerServicesProvider(new DummyServicesProvider())
    ->registerServicesProvider(new RepositoryProvider())
;
$container = $containerBuilder->build();

/** @var WebApplication $app */
$app = $container->get(WebApplication::class);
$app->setRoutes(
    require_once $basePath . '/src/Http/Routing/routes.php'
);
$app->run();
