<?php

declare(strict_types=1);

use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

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

if ($_ENV['debug'] === 'true') {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL ^ E_NOTICE);
}

$_ENV['basepath'] = __DIR__;
