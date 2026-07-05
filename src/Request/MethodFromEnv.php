<?php

declare(strict_types=1);

namespace PhpResponses\Request;

use PhpResponses\Text;

final class MethodFromEnv implements Text {
    public function string(): string {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
}
