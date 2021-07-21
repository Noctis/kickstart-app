<?php

declare(strict_types=1);

namespace App\Configuration;

interface FancyConfigurationInterface
{
    public function getDBHost(): string;

    public function getDBUser(): string;

    public function getDBPass(): string;

    public function getDBName(): string;

    public function getDBPort(): int;
}
