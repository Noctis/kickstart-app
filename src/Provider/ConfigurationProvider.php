<?php

declare(strict_types=1);

namespace App\Provider;

use App\Configuration\FancyConfiguration;
use App\Configuration\FancyConfigurationInterface;
use Noctis\KickStart\Provider\ServicesProviderInterface;

final class ConfigurationProvider implements ServicesProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getServicesDefinitions(): array
    {
        return [
            FancyConfigurationInterface::class => FancyConfiguration::class,
        ];
    }
}
