<?php

declare(strict_types=1);

namespace PhpResponse;

final class ProjectPath implements Text
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function string(): string
    {
        $dir = __DIR__;
        while ($dir !== dirname($dir)) {
            if (file_exists($dir . DIRECTORY_SEPARATOR . 'composer.json')) {
                return $dir . DIRECTORY_SEPARATOR . ltrim($this->path, '/\\');
            }
            $dir = dirname($dir);
        }
        
        return getcwd() . DIRECTORY_SEPARATOR . ltrim($this->path, '/\\');
    }
}
