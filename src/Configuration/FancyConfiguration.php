<?php

declare(strict_types=1);

namespace App\Configuration;

use Noctis\KickStart\Configuration\Configuration;

final class FancyConfiguration implements FancyConfigurationInterface
{
    public function getDBHost(): string
    {
        /** @var string */
        return Configuration::get('db_host');
    }

    public function getDBUser(): string
    {
        /** @var string */
        return Configuration::get('db_user');
    }

    public function getDBPass(): string
    {
        /** @var string */
        return Configuration::get('db_pass');
    }

    public function getDBName(): string
    {
        /** @var string */
        return Configuration::get('db_name');
    }

    public function getDBPort(): int
    {
        return (int)Configuration::get('db_port');
    }
}
