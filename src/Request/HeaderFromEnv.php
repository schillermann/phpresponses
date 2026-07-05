<?php

declare(strict_types=1);

namespace PhpResponses\Request;

use PhpResponses\Text;

final class HeaderFromEnv implements Text {
    
    private string $name;

    // name: (e.g., 'User-Agent')
    public function __construct(string $name) {
        $this->name = $name;
    }

    public function string(): string {
        $key = strtoupper(str_replace('-', '_', $this->name));
        
        if (!in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'], true)) {
            $key = 'HTTP_' . $key;
        }

        if (!isset($_SERVER[$key])) {
            throw new \OutOfBoundsException("Header '{$this->name}' is missing from the environment.");
        }

        return (string) $_SERVER[$key];
    }
}
