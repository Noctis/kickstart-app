<?php declare(strict_types=1);

use App\Http\Application;
use App\Http\Routes\StandardRoutes;

require_once __DIR__ . '/../bootstrap.php';

$app = new Application(
    new StandardRoutes()
);
$app->run();
