<?php

declare(strict_types=1);

use App\Provider\DatabaseConnectionProvider;
use App\Provider\DummyServicesProvider;
use App\Provider\HttpMiddlewareProvider;
use App\Provider\RepositoryProvider;
use Noctis\KickStart\Http\Routing\RouteInterface;
use Noctis\KickStart\Http\WebApplication;
use Noctis\KickStart\Provider\RoutingProvider;

require_once __DIR__ . '/../bootstrap.php';

$app = WebApplication::boot(
    new RoutingProvider(),
    new DatabaseConnectionProvider(),
    new HttpMiddlewareProvider(),
    new DummyServicesProvider(),
    new RepositoryProvider()
);

/** @var list<RouteInterface> $routes */
$routes = require_once __DIR__ . '/../config/routes.php';
$app->setRoutes($routes);
$app->run();
