<?php

namespace FlareGramster\Image;

interface Graphic
{
    public function process($uri);

    public function getFilename();

    public function getWidth();

    public function getHeight();

    public function getMime();

    public function getExifData();

    public function delete();
}
