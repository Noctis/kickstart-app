<?php declare(strict_types=1);
namespace App\Provider;

use App\Repository\DummyRepository;
use App\Repository\DummyRepositoryInterface;
use App\Service\DummyService;
use App\Service\DummyServiceInterface;
use Noctis\KickStart\Provider\ServicesProviderInterface;
use function DI\autowire;

final class DummyServicesProvider implements ServicesProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getServicesDefinitions(): array
    {
        return [
            DummyRepositoryInterface::class => autowire(DummyRepository::class),
            DummyServiceInterface::class => autowire(DummyService::class),
        ];
    }
}