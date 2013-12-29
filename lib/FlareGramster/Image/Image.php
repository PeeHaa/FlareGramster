<?php

namespace FlareGramster\Image;

class Image implements Graphic
{
    private $directory;

    private $filename;

    private $width;

    private $height;

    private $mime;

    public function __construct($directory)
    {
        if (!is_dir($directory)) {
            throw new InvalidDirectoryException('Invalid input directory (' . $directory . ') specified.');
        }

        $this->directory = $directory;
    }

    public function process($uri)
    {
        $this->storeLocally($uri);

        $info = getimagesize($this->filename);

        $this->width  = $info[0];
        $this->height = $info[1];
        $this->mime   = $info['mime'];
    }

    private function storeLocally($uri)
    {
        $this->filename  = $this->directory . '/' . uniqid('TMP', true) . '.jpg';

        file_put_contents($this->filename, file_get_contents($uri));
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getMime()
    {
        return $this->mime;
    }

    public function getExifData()
    {
        $exif = exif_read_data($this->filename);

        if ($exif !== false) {
            return $exif;
        }

        return [];
    }

    public function delete()
    {
        unlink($this->filename);
    }
}
