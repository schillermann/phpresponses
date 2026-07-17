<?php

declare(strict_types=1);

namespace PhpResponse\Log;

final class LevelLog implements Log
{
    private Log $origin;

    /**
     * @var array<int, string>
     */
    private array $allowed;

    public function __construct(Log $origin, string ...$allowed)
    {
        $this->origin = $origin;
        $this->allowed = $allowed;
    }

    public function write(LogEntry $entry): void
    {
        if (in_array($entry->level()->string(), $this->allowed, true)) {
            $this->origin->write($entry);
        }
    }
}
