<?php

declare(strict_types=1);

namespace PhpResponse\Request;

use PhpResponse\Text;

final class QueryParamFromEnv implements Text {
    
    private string $key;

    public function __construct(string $key) {
        $this->key = $key;
    }

    public function string(): string {
        if (!isset($_GET[$this->key])) {
            throw new \OutOfBoundsException("Query parameter '{$this->key}' is missing.");
        }

        return (string) $_GET[$this->key];
    }
}
