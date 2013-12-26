<?php

namespace FlareGramster\Storage\Filesystem;

interface Storage
{
    public function getFilename($filename);

    public function rename($oldName, $newName);
}
