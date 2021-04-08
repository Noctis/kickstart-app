<?php

declare(strict_types=1);

use App\Http\Action\DummyAction;
use App\Http\Middleware\Guard\DummyGuard;

return [
    ['GET', '/', DummyAction::class, [DummyGuard::class]],
];
