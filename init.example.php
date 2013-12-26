<?php
/**
 * Example environment file
 *
 * This file sets up the environment under which the application runs
 */

/**
 * Setup error reporting
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 0);

/**
 * Setup config
 */
$settings = [
    'debugging'  => true,
    'executable' => 'C:\Progra~1\ImageMagick-6.8.7-Q16\convert.exe',
    'gaCode'     => null,
    'gaDomain'   => null,
    'dbDsn'      => 'pgsql:dbname=f;aregramster;host=127.0.0.1;charset=utf8',
    'dbUsername' => 'username',
    'dbPassword' => 'password',
];
