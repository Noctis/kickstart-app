<?php

declare(strict_types=1);

use App\Http\Application;

require_once __DIR__ . '/../bootstrap.php';

$app = new Application(
    require_once __DIR__ . '/../src/Http/Routing/routes.php'
);
$app->run();
