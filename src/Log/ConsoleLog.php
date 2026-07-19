<?php

declare(strict_types=1);

namespace PhpResponse\Log;

use PhpResponse\Text\LiteralText;

final class ConsoleLog implements Log
{
    private Log $origin;

    public function __construct()
    {
        $this->origin = new FileLog(new LiteralText('php://stdout'));
    }

    public function write(LogEntry $entry): void
    {
        $this->origin->write($entry);
    }
}
