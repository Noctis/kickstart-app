<?php

declare(strict_types=1);

use App\Provider\DatabaseConnectionProvider;
use App\Provider\DummyServicesProvider;
use App\Provider\HttpMiddlewareProvider;
use App\Provider\RepositoryProvider;
use Noctis\KickStart\Configuration\Configuration;
use Noctis\KickStart\Http\WebApplication;
use Noctis\KickStart\Provider\RoutingProvider;

require_once __DIR__ . '/../bootstrap.php';

/** @var $basePath string */
$basePath = Configuration::get('basepath');
$app = WebApplication::boot([
    new RoutingProvider(),
    new DatabaseConnectionProvider(),
    new HttpMiddlewareProvider(),
    new DummyServicesProvider(),
    new RepositoryProvider()
]);
$app->setRoutes(
    require_once $basePath . '/src/Http/Routing/routes.php'
);
$app->run();
