<?php

declare(strict_types=1);

namespace PhpResponse\Log;

interface Log
{
    public function write(LogEntry $entry): void;
}
