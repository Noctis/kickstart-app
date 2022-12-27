<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Noctis\KickStart\Configuration\Configuration;
use Noctis\KickStart\Debugging;

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
])
->notEmpty();
$dotenv->required('db_port')
    ->notEmpty()
    ->isInteger();

Configuration::isProduction()
    ? Debugging::off()
    : Debugging::on();

$_ENV['basepath'] = __DIR__;
