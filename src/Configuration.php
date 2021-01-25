<?php declare(strict_types=1);
namespace App;

use Dotenv\Dotenv;

final class Configuration
{
    /** @var string[] */
    private array $requiredOptions = [
        'basepath',
        'db_host',
        'db_user',
        'db_pass',
        'db_name',
        'db_port',
        'dummy_param',
    ];

    public function load(string $path): void
    {
        $dotenv = Dotenv::createImmutable($path);
        $dotenv->load();

        $dotenv->required($this->requiredOptions)
            ->notEmpty();

        $dotenv->required('dummy_param')
            ->isBoolean();
    }
}