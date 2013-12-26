<?php

namespace FlareGramster\Image;

class Image implements Graphic
{
    private $filename;

    private $width;

    private $height;

    private $mime;

    public function __construct($filename)
    {
        if (!is_file($filename)) {
            throw new InvalidImageException('File (' . $filename . ') not found.');
        }

        $this->filename = $filename;
    }

    public function processInfo()
    {
        $info = getimagesize($this->filename);

        $this->width  = $info[0];
        $this->height = $info[1];
        $this->mime   = $info['mime'];
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
