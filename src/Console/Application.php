<?php declare(strict_types=1);
namespace App\Console;

use App\Provider\ConfigurationProvider;
use App\Provider\DatabaseConnectionProvider;
use App\Provider\DummyServicesProvider;
use Noctis\KickStart\Console\AbstractConsoleApplication;

final class Application extends AbstractConsoleApplication
{
    protected function getServiceProviders(): array
    {
        return array_merge(
            parent::getServiceProviders(),
            [
                new ConfigurationProvider(),
                new DatabaseConnectionProvider(),
                new DummyServicesProvider(),
            ]
        );
    }
}