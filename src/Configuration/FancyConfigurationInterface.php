<?php

declare(strict_types=1);

namespace App\Configuration;

use Noctis\KickStart\Configuration\ConfigurationInterface;

interface FancyConfigurationInterface extends ConfigurationInterface
{
    public function getBaseHref(): string;

    public function getDBHost(): string;

    public function getDBUser(): string;

    public function getDBPass(): string;

    public function getDBName(): string;

    public function getDBPort(): int;

    public function getDummyParam(): bool;
}
