<?php

namespace FlareGramster\Image;

interface Data
{
    public function getUserId();

    public function getIp();

    public function getUri();

    public function getWidth();

    public function getHeight();

    public function getMime();

    public function getExif();

    public function getHash();
}
