<?php

namespace FlareGramster\Image;

interface Graphic
{
    public function processInfo();

    public function getFilename();

    public function getWidth();

    public function getHeight();

    public function getMime();

    public function getExifData();

    public function delete();
}
