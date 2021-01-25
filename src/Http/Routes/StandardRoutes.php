<?php declare(strict_types=1);
namespace App\Http\Routes;

use App\Http\Action\DummyAction;
use App\Http\Middleware\Guard\DummyGuard;
use FastRoute\RouteCollector;

final class StandardRoutes
{
    public function get(): callable
    {
        return function (RouteCollector $r): void {
            $r->addGroup(
                $_ENV['basepath'],
                function (RouteCollector $r) {
                    $r->get('[{name}]', [
                        DummyAction::class,
                        [
                            DummyGuard::class,
                        ],
                    ]);
                }
            );
        };
    }
}