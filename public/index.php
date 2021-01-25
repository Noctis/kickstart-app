<?php declare(strict_types=1);

use App\Http\Routes\StandardRoutes;
use Noctis\KickStart\Http\Router;
use function FastRoute\simpleDispatcher;

require_once __DIR__ . '/../bootstrap.php';

$dispatcher = simpleDispatcher(
    (new StandardRoutes())->get()
);

$container = get_container($env);
$router = new Router($dispatcher, $container);
$router->route();