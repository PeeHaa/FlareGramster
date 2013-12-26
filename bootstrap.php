<?php

namespace FlareGramster;

use FlareGramster\Common\Autoloader;
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageProcessor = new ImageMagick($settings['executable']);

    $input  = uniqid('TMP', true) . '.jpg';

    file_put_contents(__DIR__ . '/images/input/' . $input, file_get_contents($_POST['url']));

    $image = new Image(__DIR__ . '/images/input/' . $input);
    $image->processInfo();

    $flareGramster = new FlareGramster($image, $imageProcessor, __DIR__ . '/images/output');
    $output = $flareGramster->process();
}

if (preg_match('#/output/(.*)$#', $_SERVER['REQUEST_URI'], $matches) === 1) {
    $filename = __DIR__ . '/images' . $matches[0];

    header('Content-type: image/png');
    header('Content-Length: ' . filesize($filename));

    echo file_get_contents($filename);
}

require_once __DIR__ . '/templates/page.phtml';
