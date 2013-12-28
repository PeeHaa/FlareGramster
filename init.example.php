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
    'dbDsn'      => 'pgsql:dbname=flaregramster;host=127.0.0.1',
    'dbUsername' => 'username',
    'dbPassword' => 'password',
    'social'     => [
        'facebook' => [
            'title'  => 'Facebook',
            'key'    => null,
            'secret' => null,
        ],
        'google' => [
            'title'  => 'Google+',
            'key'    => null,
            'secret' => null,
        ],
        'twitter' => [
            'title'  => 'Twitter',
            'key'    => null,
            'secret' => null,
        ],
        'pinterest' => [
            'title'  => 'Pinterest',
            'key'    => null,
            'secret' => null,
        ],
        'stumbleupon' => [
            'title'  => 'StumbleUpon',
            'key'    => null,
            'secret' => null,
        ],
        'digg' => [
            'title'  => 'Digg',
            'key'    => null,
            'secret' => null,
        ],
        'forrst' => [
            'title'  => 'Forrst',
            'key'    => null,
            'secret' => null,
        ],
        'reddit' => [
            'title'  => 'Reddit',
            'key'    => null,
            'secret' => null,
        ],
        'delicious' => [
            'title'  => 'Delicious',
            'key'    => null,
            'secret' => null,
        ],
        'flickr' => [
            'title'  => 'Flickr',
            'key'    => null,
            'secret' => null,
        ],
        'behance' => [
            'title'  => 'Behance',
            'key'    => null,
            'secret' => null,
        ],
        'instagram' => [
            'title'  => 'Instagram',
            'key'    => null,
            'secret' => null,
        ],
        'dribble' => [
            'title'  => 'Dribble',
            'key'    => null,
            'secret' => null,
        ],
    ],
];
