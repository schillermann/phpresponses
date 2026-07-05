<?php

declare(strict_types=1);

namespace PhpResponses\Request;

use PhpResponses\Text;

final class ProtocolFromEnv implements Text {
    public function string(): string {
        return $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
    }
}
