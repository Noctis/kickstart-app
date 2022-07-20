<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Noctis\KickStart\Configuration\Configuration;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run as Whoops;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
$dotenv->required([
    'APP_ENV',
    'basehref',
    'db_host',
    'db_user',
    'db_pass',
    'db_name',
    'db_port'
]);
$dotenv->required('db_port')->isInteger();

$whoops = new Whoops();
if (Configuration::isProduction()) {
    ini_set('display_errors', 'Off');
} else {
    ini_set('display_errors', 'On');
    $whoops->pushHandler(new PrettyPageHandler());
}
$whoops->register();

$_ENV['basepath'] = __DIR__;
