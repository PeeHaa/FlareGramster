<?php

namespace FlareGramster;

use FlareGramster\Common\Autoloader;
use FlareGramster\Storage\ImmutableArray;
use FlareGramster\Network\Http\Request;
use FlareGramster\Image\Process\ImageMagick;
use FlareGramster\Image\Image;
use FlareGramster\Image\Process\FlareGramster;

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
 * Setup router
 */
if ($request->getMethod() === 'POST') {
    $imageProcessor = new ImageMagick($settings['executable']);

    $input  = uniqid('TMP', true) . '.jpg';

    file_put_contents(__DIR__ . '/images/input/' . $input, file_get_contents($_POST['url']));

    $image = new Image(__DIR__ . '/images/input/' . $input);
    $image->processInfo();

    $flareGramster = new FlareGramster($image, $imageProcessor, __DIR__ . '/images/output');
    $output = $flareGramster->process();

    $image->delete();

    if ($request->isXhr()) {
        echo json_encode([
            'imageUri' => '/output/' . $output,
        ]);

        exit;
    }
}

if (preg_match('#/output/(.*)$#', $_SERVER['REQUEST_URI'], $matches) === 1) {
    $filename = __DIR__ . '/images' . $matches[0];

    header('Content-type: image/png');
    header('Content-Length: ' . filesize($filename));

    echo file_get_contents($filename);
}

require_once __DIR__ . '/templates/page.phtml';
