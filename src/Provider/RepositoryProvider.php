<?php

declare(strict_types=1);

namespace App\Provider;

use App\Repository\DummyRepository;
use App\Repository\DummyRepositoryInterface;
use Noctis\KickStart\Provider\ServicesProviderInterface;

final class RepositoryProvider implements ServicesProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getServicesDefinitions(): array
    {
        return [
            DummyRepositoryInterface::class => DummyRepository::class,
        ];
    }
}
