<?php

declare(strict_types=1);

namespace App\Provider;

use Noctis\KickStart\Configuration\Configuration;
use Noctis\KickStart\Provider\ServicesProviderInterface;
use ParagonIE\EasyDB\EasyDB;
use ParagonIE\EasyDB\Exception\ConstructorFailed;
use ParagonIE\EasyDB\Factory;

final class DatabaseConnectionProvider implements ServicesProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getServicesDefinitions(): array
    {
        return [
            EasyDB::class => function (): EasyDB {
                try {
                    /** @psalm-suppress MixedArgument */
                    return Factory::fromArray([
                        sprintf(
                            'mysql:dbname=%s;host=%s;port=%s',
                            Configuration::get('db_name'),
                            Configuration::get('db_host'),
                            Configuration::get('db_port')
                        ),
                        Configuration::get('db_user'),
                        Configuration::get('db_pass')
                    ]);
                } catch (ConstructorFailed $ex) {
                    die('Could not connect to primary DB: ' . $ex->getMessage());
                }
            },
        ];
    }
}
