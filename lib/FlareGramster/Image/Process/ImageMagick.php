<?php

namespace FlareGramster\Image\Process;

class ImageMagick implements Executable
{
    private $executable;

    public function __construct($executable)
    {
        if (!is_executable($executable)) {
            throw new InvalidExecutableException('Invalid ImageMagick executable (' . $executable . ') provided.');
        }

        $this->executable = $executable;
    }

    public function execute($command)
    {
        $fullCommand = $this->executable . ' ' . $this->normalize($command);

        shell_exec(escapeshellcmd($fullCommand));
    }

    private function normalize($command)
    {
        return preg_replace('#(\s){2,}#is', '', str_replace(["\n", "'"], ['', '"'], $command));
    }
}
