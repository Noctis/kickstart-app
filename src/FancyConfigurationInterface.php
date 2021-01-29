<?php declare(strict_types=1);
namespace App;

use Noctis\KickStart\Configuration\ConfigurationInterface;

interface FancyConfigurationInterface extends ConfigurationInterface
{
    public function getBasePath(): string;

    public function getDbHost(): string;

    public function getDbUser(): string;

    public function getDbPass(): string;

    public function getDbPort(): int;

    public function getDummyParam(): bool;
}