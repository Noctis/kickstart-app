<?php

declare(strict_types=1);

use App\Http\Action\DummyAction;
use App\Http\Middleware\Guard\DummyGuard;
use Noctis\KickStart\Http\Routing\Route;

return [
    Route::get('/', DummyAction::class, [DummyGuard::class]),

    // Example route: the `id` parameter is required and must be a number
    //Route::get('/user/{id:\d+}', DummyAction::class),

    // Example route: the `title` parameter is required (as is the `/` in front of it)
    //Route::get('/project[/{title}]', DummyAction::class),
];
