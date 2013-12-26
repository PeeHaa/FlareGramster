<?php

namespace FlareGramster\Image;

interface Graphic
{
    public function processInfo();

    public function getFilename();

    public function getWidth();

    public function getHeight();
}
