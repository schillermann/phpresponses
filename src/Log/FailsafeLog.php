<?php

declare(strict_types=1);

namespace PhpResponse\Log;

final class FailsafeLog implements Log
{
    private Log $origin;

    public function __construct(Log $origin)
    {
        $this->origin = $origin;
    }

    public function write(LogEntry $entry): void
    {
        try {
            $this->origin->write($entry);
        } catch (\Throwable $e) {
            error_log($e->getMessage());
        }
    }
}
