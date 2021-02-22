<?php declare(strict_types=1);

use App\FancyConfiguration;
use Noctis\KickStart\Configuration\ConfigurationLoader;

require_once __DIR__ . '/vendor/autoload.php';

$env = 'dev';
if ($env === 'dev') {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL ^ E_NOTICE);
}
$_ENV['BASEDIR'] = __DIR__;

(new ConfigurationLoader())
    ->load(__DIR__, FancyConfiguration::REQUIREMENTS);
