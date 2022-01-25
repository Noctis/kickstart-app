<?php

declare(strict_types=1);

namespace App\Provider;

use App\Configuration\FancyConfiguration;
use App\Configuration\FancyConfigurationInterface;
use Noctis\KickStart\Configuration\ConfigurationInterface;
use Noctis\KickStart\Provider\ServicesProviderInterface;
use Psr\Container\ContainerInterface;

final class ConfigurationProvider implements ServicesProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getServicesDefinitions(): array
    {
        return [
            FancyConfigurationInterface::class => function (ContainerInterface $container): FancyConfiguration {
                /** @var ConfigurationInterface $baseConfiguration */
                $baseConfiguration = $container->get(ConfigurationInterface::class);

                return new FancyConfiguration($baseConfiguration);
            },
        ];
    }
}