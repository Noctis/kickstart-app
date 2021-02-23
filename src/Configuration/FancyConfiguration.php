<?php declare(strict_types=1);
namespace App\Configuration;

use Noctis\KickStart\Configuration\ConfigurationInterface;

final class FancyConfiguration implements FancyConfigurationInterface
{
    private ConfigurationInterface $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getBaseHref(): string
    {
        return $this->get('basehref');
    }

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

    public function getDummyParam(): bool
    {
        return $this->get('dummy_param') === 'true';
    }

    public function get(string $name, $default = null)
    {
        return $this->configuration
            ->get($name, $default);
    }

    public function set(string $name, mixed $value): void
    {
        $this->configuration
            ->set($name, $value);
    }

    public function has(string $name): bool
    {
        return $this->configuration
            ->has($name);
    }
}