<?php declare(strict_types=1);
namespace App\Http\Routes;

use App\Configuration\FancyConfigurationInterface;
use App\Http\Action\DummyAction;
use App\Http\Middleware\Guard\DummyGuard;
use FastRoute\RouteCollector;
use Noctis\KickStart\Http\Routing\HttpRoutesProviderInterface;

final class StandardRoutes implements HttpRoutesProviderInterface
{
    private FancyConfigurationInterface $configuration;

    public function __construct(FancyConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function get(): callable
    {
        return function (RouteCollector $r): void {
            $baseHref = $this->configuration
                ->getBaseHref();

            $r->addGroup(
                $baseHref,
                function (RouteCollector $r) {
                    $r->get('/[{name}]', [
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