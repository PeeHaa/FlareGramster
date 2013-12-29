<?php

namespace FlareGramster;

use FlareGramster\Common\Autoloader;
use FlareGramster\Social\OAuth;
use FlareGramster\Storage\ImmutableArray;
use FlareGramster\Network\Http\Request;
use FlareGramster\Image\Process\ImageMagick;
use FlareGramster\Image\Image;
use FlareGramster\Storage\Filesystem\Segmented;
use FlareGramster\Image\Process\FlareGramster;
use FlareGramster\Storage\Database\Image as ImageStorage;
use FlareGramster\Identifier\Converter;
use FlareGramster\Html\Meta;
use FlareGramster\Image\DomainObject;

/**
 * Setup the library autoloader
 */
require_once __DIR__ . '/lib/FlareGramster/Common/Autoloader.php';

$autoloader = new Autoloader(__NAMESPACE__, __DIR__ . '/lib');
$autoloader->register();

/**
 * Setup composer libraries
 */
require_once __DIR__ . '/vendor/autoload.php';

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
 * Setup the environment
 */
require_once __DIR__ . '/init.deployment.php';

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
 * Setup id converter
 */
$identifier = new Converter();

/**
 * Setup base meta tags
 */
$metaTags = new Meta(
    'FlareGramster',
    [
        'Turn your crappy scenic photos into oversaturated photos with a douchey hipster on it!',
        'You can share this photo and show everyone how you think about deep things like seagulls and clouds and shit!',
    ],
    ['flaregramster', 'oatmeal', 'instagram', 'hipster', 'flare', 'image', 'photo', 'scenic'],
    $request->getBaseUrl() . $request->getPath()
);

/**
 * Setup router
 */
if ($request->getMethod() === 'POST' && $request->getPath() === '/') {
    $image = new Image(__DIR__ . '/images/input');
    $image->process($request->post('url'));

    $imageProcessor = new ImageMagick($settings['executable']);

    $flareGramster = new FlareGramster($image, $imageProcessor, $outputDirectory);
    $hash = $flareGramster->process();

    $imageData = new DomainObject($request, $image, $hash);

    $imageStorage = new ImageStorage($dbConnection);
    $id = $imageStorage->persistImage($imageData);

    $image->delete();

    header('Location: ' . $request->getBaseUrl() . '/' . $identifier->toHash($id));
    exit;
}

if (preg_match('#^/[a-z0-9]+/share/(.*)$#i', $request->getPath(), $matches) === 1 && $request->getMethod() === 'GET') {
    if ($request->get('code')) {
        $oAuthServices->get($matches[1])->getAccessToken($request->get('code'));

        header('Location: ' . $request->getBaseUrl() . $request->getPath());
        exit;
    } else {
        $socialForm = true;
    }
} elseif (preg_match('#^/([a-z0-9]+)/share/(.*)$#i', $request->getPath(), $matches) === 1 && $request->getMethod() === 'POST') {
    $oauth->setUp($_SERVER);

    $oauth->postMessage($matches[2], $request->getBaseUrl() . '/' . $matches[1], $request->post('message'));

    header('Location: ' . $request->getBaseUrl() . '/' . $matches[1]);
    exit;
}

if (preg_match('#^/output/(.*)$#', $request->getPath(), $matches) === 1) {
    header('Content-type: image/png');
    header('Content-Length: ' . filesize($outputDirectory->getFilename($matches[1])));

    echo file_get_contents($outputDirectory->getFilename($matches[1]));
} elseif ($request->getPath() !== '/') {
    $pathParts = explode('/', trim($request->getPath(), '/'));
    $hashedId = $pathParts[0];
    $id       = $identifier->toPlain($hashedId);

    $imageStorage = new ImageStorage($dbConnection);
    $hash = $imageStorage->getHash($id);

    $output = $hash . '.png';

    if ($request->isXhr()) {
        echo json_encode([
            'imageUri' => '/output/' . $output,
            'hash'     => $hashedId,
        ]);

        exit;
    }

    $metaTags->setTwitterType('photo');
    $metaTags->setImage($request->getBaseUrl() . '/output/' . $output);
}

require_once __DIR__ . '/templates/page.phtml';
