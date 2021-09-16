<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Noctis\KickStart\Configuration\Configuration;

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

if (!Configuration::isProduction()) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL ^ E_NOTICE);
}

$_ENV['basepath'] = __DIR__;
