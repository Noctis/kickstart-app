<?php declare(strict_types=1);

use App\FancyConfiguration;
use App\Provider\ConfigurationProvider;
use App\Provider\DatabaseConnectionProvider;
use App\Provider\DummyServicesProvider;
use App\Provider\HttpMiddlewareProvider;
use Noctis\KickStart\Configuration\ConfigurationLoader;
use Noctis\KickStart\ContainerBuilder;
use Psr\Container\ContainerInterface;

require_once __DIR__ . '/vendor/autoload.php';

$env = 'dev';
if ($env === 'dev') {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL ^ E_NOTICE);
}
$_ENV['BASEDIR'] = __DIR__;

(new ConfigurationLoader())
    ->load(__DIR__, FancyConfiguration::REQUIREMENTS);

function get_container(string $env): ContainerInterface
{
    $containerBuilder = new ContainerBuilder(__DIR__, $env);
    /** @todo move adding user services provider to a better place */
    $containerBuilder->addServicesProviders([
        new ConfigurationProvider(),
        new DatabaseConnectionProvider(),
        new HttpMiddlewareProvider(),
        new DummyServicesProvider(),
    ]);

    return $containerBuilder->build();
}