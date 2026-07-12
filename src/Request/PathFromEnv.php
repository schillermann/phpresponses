<?php

declare(strict_types=1);

namespace PhpResponse\Request;

use PhpResponse\Text;

final class PathFromEnv implements Text {
    
    public function string(): string {
        if (!isset($_SERVER['REQUEST_URI'])) {
            return '/';
        }

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        return $path !== false ? (string) $path : '/';
    }
}
