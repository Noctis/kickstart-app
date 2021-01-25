<?php declare(strict_types=1);
namespace App\Provider;

use App\Repository\DummyRepository;
use App\Repository\DummyRepositoryInterface;
use App\Service\DummyService;
use App\Service\DummyServiceInterface;
use Noctis\KickStart\Provider\ServicesProviderInterface;

final class DummyServicesProvider implements ServicesProviderInterface
{
    /**
     * @return mixed[]
     */
    public function getServicesDefinitions(): array
    {
        return [
            DummyRepositoryInterface::class => DummyRepository::class,
            DummyServiceInterface::class => DummyService::class,
        ];
    }
}