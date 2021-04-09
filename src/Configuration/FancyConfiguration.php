<?php

declare(strict_types=1);

namespace App\Configuration;

use Noctis\KickStart\Configuration\Configuration;

final class FancyConfiguration extends Configuration implements FancyConfigurationInterface
{
    public function getDBHost(): string
    {
        return $this->get('db_host');
    }

    public function getDBUser(): string
    {
        return $this->get('db_user');
    }

    public function getDBPass(): string
    {
        return $this->get('db_pass');
    }

    public function getDBName(): string
    {
        return $this->get('db_name');
    }

    public function getDBPort(): int
    {
        return (int)$this->get('db_port');
    }
}
