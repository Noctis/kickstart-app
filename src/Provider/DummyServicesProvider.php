<?php declare(strict_types=1);
namespace App\Provider;

use App\Service\DummyService;
use App\Service\DummyServiceInterface;
use Noctis\KickStart\Provider\ServicesProviderInterface;

final class DummyServicesProvider implements ServicesProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getServicesDefinitions(): array
    {
        return [
            DummyServiceInterface::class => DummyService::class,
        ];
    }
}