<?php

declare(strict_types=1);

namespace PhpResponse\Text;

use PhpResponse\Text;

final class TextOfFile implements Text {
    
    private string $path;

    public function __construct(string $path) {
        $this->path = $path;
    }

    public function string(): string {
        if (!file_exists($this->path)) {
            throw new \InvalidArgumentException("File '{$this->path}' not found.");
        }
        
        $content = file_get_contents($this->path);
        if ($content === false) {
            throw new \RuntimeException("Failed to read file '{$this->path}'.");
        }
        
        return $content;
    }
}
