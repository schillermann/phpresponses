<?php

declare(strict_types=1);

namespace PhpResponse\Log;

final class TeeLog implements Log
{
    /**
     * @var array<int, Log>
     */
    private array $logs;

    public function __construct(Log ...$logs)
    {
        $this->logs = $logs;
    }

    public function write(LogEntry $entry): void
    {
        foreach ($this->logs as $log) {
            $log->write($entry);
        }
    }
}
