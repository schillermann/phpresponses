<?php

declare(strict_types=1);

namespace PhpResponse\Request;

use PhpResponse\Text;

final class BodyFromEnv implements Text {
    
    public function string(): string {
        $body = file_get_contents('php://input');

        if ($body === false) {
            throw new \RuntimeException('Failed to read the request body from the environment.');
        }

        return $body;
    }
}
