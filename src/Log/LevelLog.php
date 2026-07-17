<?php

declare(strict_types=1);

namespace PhpResponse\Log;

use PhpResponse\Log\Level\LogLevel;

final class LevelLog implements Log
{
    private Log $origin;

    /**
     * @var array<int, LogLevel>
     */
    private array $allowed;

    public function __construct(Log $origin, LogLevel ...$allowed)
    {
        $this->origin = $origin;
        $this->allowed = $allowed;
    }

    public function write(LogEntry $entry): void
    {
        foreach ($this->allowed as $allowedLevel) {
            if ($entry->level()->string() === $allowedLevel->string()) {
                $this->origin->write($entry);
                return;
            }
        }
    }
}
