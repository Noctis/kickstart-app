<?php

declare(strict_types=1);

use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

$env = 'dev';
if ($env === 'dev') {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL ^ E_NOTICE);
}

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
$dotenv->required([
    'debug',
    'basehref',
    'db_host',
    'db_user',
    'db_pass',
    'db_name',
    'db_port'
]);
$dotenv->required('debug')->isBoolean();
$dotenv->required('db_port')->isInteger();

$_ENV['basepath'] = __DIR__;
