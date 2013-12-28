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

require_once __DIR__ . '/init.deployment.php';

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
 * Setup the oAuth library
 */
$oauth = new OAuth($settings['social']);

/**
 * Setup id converter
 */
$identifier = new Converter();

/**
 * Setup base meta tags
 */
$metaTags = [
    'name' => [
        'description'    => 'Turn your crappy scenic photos into oversaturated photos with a douchey hipster on it! You can share this photo and show everyone how you think about deep things like seagulls and clouds and shit!',
        'keywords'       => 'flaregramster,oatmeal,instagram,hipster,flare,image,photo,scenic',
        'twitter:card'   => 'summary',
        'twitter:title'  => 'Turn your crappy scenic photos into oversaturated photos with a douchey hipster on it!',
        'twitter:image'  => null,
    ],
    'property' => [
        'og:title'       => 'FlareGramster',
        'og:url'         => 'https://flaregramster.pieterhordijk.com',
        'og:site_name'   => 'FlareGramster',
        'og:type'        => null,
        'og:image'       => null,
        'og:local'       => 'en_US',
        'og:description' => 'Turn your crappy scenic photos into oversaturated photos with a douchey hipster on it!',
    ],
];

/**
 * Setup router
 */
if ($request->getMethod() === 'POST' && $request->getPath() === '/') {
    $imageProcessor = new ImageMagick($settings['executable']);

    $input  = uniqid('TMP', true) . '.jpg';

    file_put_contents(__DIR__ . '/images/input/' . $input, file_get_contents($request->post('url')));

    $image = new Image(__DIR__ . '/images/input/' . $input);
    $image->processInfo();

    $flareGramster = new FlareGramster($image, $imageProcessor, $outputDirectory);
    $hash = $flareGramster->process();

    $data = [
        'userid' => null,
        'ip'     => $request->server('REMOTE_ADDR'),
        'uri'    => $request->post('url'),
        'width'  => $image->getWidth(),
        'height' => $image->getHeight(),
        'mime'   => $image->getMime(),
        'exif'   => json_encode($image->getExifData()),
        'image'  => $hash,
    ];

    $imageStorage = new ImageStorage($dbConnection);
    $id = $imageStorage->persistImage($data);

    $image->delete();

    header('Location: ' . $request->getBaseUrl() . '/' . $identifier->toHash($id));
    exit;
}

if (preg_match('#^/[a-z0-9]+/share/(.*)$#i', $request->getPath(), $matches) === 1 && $request->getMethod() === 'GET') {
    if ($request->get('code')) {
        $oauth->setUp($_SERVER);

        $oauth->getAccessToken($matches[1], $request->get('code'));

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

    $metaTags['name']['twitter:card']  = 'photo';
    $metaTags['name']['twitter:image'] = $request->getBaseUrl() . '/output/' . $output;
    $metaTags['property']['og:image']  = $request->getBaseUrl() . '/output/' . $output;

    $oauth->setUp($_SERVER);
}

require_once __DIR__ . '/templates/page.phtml';
