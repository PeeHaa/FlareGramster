<?php

namespace FlareGramster\Storage\Filesystem;

class Segmented implements Storage
{
    private $workingDirectory;

    public function __construct($workingDirectory)
    {
        $this->workingDirectory = rtrim($workingDirectory, '/');
    }

    public function getFilename($filename)
    {
        return $this->workingDirectory . '/' . implode('/', $this->getSegmentsFromFilename($filename)) . '/' . $filename;
    }

    public function rename($oldName, $newName)
    {
        $directory = $this->workingDirectory . '/' . implode('/', $this->getSegmentsFromFilename($newName));

        if (!is_dir($directory)) {
            mkdir($directory, 0760, true);
        }

        rename($oldName, $directory . '/' . $newName);
    }

    private function getSegmentsFromFilename($filename)
    {
        return str_split(pathinfo($filename, PATHINFO_FILENAME), 4);
    }
}
