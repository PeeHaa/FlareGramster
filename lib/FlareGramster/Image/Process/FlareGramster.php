<?php

namespace FlareGramster\Image\Process;

use FlareGramster\Image\Graphic;
use FlareGramster\Image\Process\Executable;
use FlareGramster\Storage\Filesystem\Storage;

class FlareGramster
{
    private $originalImage;

    private $imageProcessor;

    private $filesystem;

    private $temporaryImage;

    public function __construct(Graphic $image, Executable $imageProcessor, Storage $filesystem)
    {
        $this->originalImage  = $image;
        $this->imageProcessor = $imageProcessor;
        $this->filesystem     = $filesystem;
    }

    public function process()
    {
        $this->createTemporaryImage();

        if ($this->originalImage->getWidth() > 612) {
            $this->resize(612, 612);
        }

        $this->addDoucheyHipster(612, 612);

        $this->overSaturate();

        $this->addLensFlare(612, 612);

        $this->addFrame(612, 612);

        $hipsteredImage = sha1_file($this->temporaryImage) . '.png';

        $this->filesystem->rename($this->temporaryImage, $hipsteredImage);

        return $hipsteredImage;
    }

    private function createTemporaryImage()
    {
        $this->temporaryImage = sys_get_temp_dir() . '/' . uniqid(mt_rand(), true) . '.png';

        copy($this->originalImage->getFilename(), $this->temporaryImage);
    }

    private function resize($width, $height)
    {
        $this->imageProcessor->execute(
            $this->originalImage->getFilename() . ' -resize ' . $width . 'x' . $height . '! -unsharp 1.5×1.0+1.5+0.02 ' . $this->temporaryImage
        );
    }

    private function addDoucheyHipster($width, $height)
    {
        $glob = glob(realpath(__DIR__ . '/../../../../images/assets/hipsters') . '/*.png');

        $hipster = $glob[(array_rand($glob))];

        $this->imageProcessor->execute(
            $this->temporaryImage . ' ( "' . $hipster . '" -resize ' . $width . 'x' . $height . '! -unsharp 1.5×1.0+1.5+0.02 ) -flatten ' . $this->temporaryImage
        );
    }

    private function overSaturate()
    {
        $this->imageProcessor->execute(
            $this->temporaryImage . ' -brightness-contrast 0x50 ' . $this->temporaryImage
        );
    }

    private function addLensFlare($width, $height)
    {
        $glob = glob(realpath(__DIR__ . '/../../../../images/assets/flares') . '/*.png');

        $flare = $glob[(array_rand($glob))];

        $this->imageProcessor->execute(
            $this->temporaryImage . '( "' . $flare . '" -resize ' . $width . 'x' . $height . '! -unsharp 1.5×1.0+1.5+0.02 ) -flatten ' . $this->temporaryImage
        );
    }

    private function addFrame($width, $height)
    {
        $frame = realpath(__DIR__ . '/../../../../images/assets/frames/Nashville.png');

        $this->imageProcessor->execute(
            $this->temporaryImage . ' ( "' . $frame . '" -resize ' . $width . 'x' . $height . '! -unsharp 1.5×1.0+1.5+0.02 ) -flatten ' . $this->temporaryImage
        );
    }
}
