<?php

declare(strict_types=1);

use App\Http\Action\DummyAction;
use App\Http\Middleware\Guard\DummyGuard;
use App\Http\Request\DummyRequest;
use Noctis\KickStart\Http\Routing\Route;

return [
    Route::get('/', DummyAction::class, [DummyGuard::class], DummyRequest::class),

    // Example route: the `id` parameter is required and must be a number
    //Route::get('/user/{id:\d+}', DummyAction::class),
    // The `id` parameter can be fetched in action by calling `$request->getAttribute('id')`

    // Example route: the `title` parameter is required (as is the `/` in front of it)
    //Route::get('/project[/{title}]', DummyAction::class),
    // The `title` parameter can be fetched in action by calling `$request->getAttribute('title')`
];
