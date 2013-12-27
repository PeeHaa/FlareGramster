<?php

namespace FlareGramster;

use FlareGramster\Common\Autoloader;
use FlareGramster\Storage\ImmutableArray;
use FlareGramster\Network\Http\Request;
use FlareGramster\Image\Process\ImageMagick;
use FlareGramster\Image\Image;
use FlareGramster\Storage\Filesystem\Segmented;
use FlareGramster\Image\Process\FlareGramster;
use FlareGramster\Storage\Database\Image as ImageStorage;

require_once __DIR__ . '/init.deployment.php';

/**
 * Setup the library autoloader
 */
require_once __DIR__ . '/lib/FlareGramster/Common/Autoloader.php';

$autoloader = new Autoloader(__NAMESPACE__, __DIR__ . '/lib');
$autoloader->register();

/**
 * Setup the request object
 */
$request = new Request(
    new ImmutableArray($_GET),
    new ImmutableArray($_POST),
    new ImmutableArray($_SERVER),
    new ImmutableArray($_FILES)
);

/**
 * Setup the output directory
 */
$outputDirectory = new Segmented(__DIR__ . '/images/output');

/**
 * Setup the database connection
 */
$dbConnection = new \PDO($settings['dbDsn'], $settings['dbUsername'], $settings['dbPassword']);
$dbConnection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
$dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

/**
 * Setup router
 */
if ($request->getMethod() === 'POST') {
    $imageProcessor = new ImageMagick($settings['executable']);

    $input  = uniqid('TMP', true) . '.jpg';

    file_put_contents(__DIR__ . '/images/input/' . $input, file_get_contents($request->post('url')));

    $image = new Image(__DIR__ . '/images/input/' . $input);
    $image->processInfo();

    $flareGramster = new FlareGramster($image, $imageProcessor, $outputDirectory);
    $output = $flareGramster->process();

    $data = [
        'userid' => null,
        'ip'     => $request->server('REMOTE_ADDR'),
        'uri'    => $request->post('url'),
        'width'  => $image->getWidth(),
        'height' => $image->getHeight(),
        'mime'   => $image->getMime(),
        'exif'   => json_encode($image->getExifData()),
    ];

    $imageStorage = new ImageStorage($dbConnection);
    $id = $imageStorage->persistImage($data);

    $image->delete();

    if ($request->isXhr()) {
        echo json_encode([
            'imageUri' => '/output/' . $output,
        ]);

        exit;
    }
}

if (preg_match('#/output/(.*)$#', $_SERVER['REQUEST_URI'], $matches) === 1) {
    header('Content-type: image/png');
    header('Content-Length: ' . filesize($outputDirectory->getFilename($matches[1])));

    echo file_get_contents($outputDirectory->getFilename($matches[1]));
}

require_once __DIR__ . '/templates/page.phtml';
