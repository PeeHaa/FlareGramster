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
    'executable' => 'C:\Progra~1\ImageMagick-6.8.7-Q16\convert.exe',
    'gaCode'     => '1234567890',
    'gaDomain'   => 'example.com',
];
