<?php

declare(strict_types=1);

namespace PhpResponse\Request;

use PhpResponse\Text;

final class MethodFromEnv implements Text {
    public function string(): string {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
}
