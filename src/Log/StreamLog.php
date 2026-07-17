<?php

declare(strict_types=1);

namespace PhpResponse\Log;

final class StreamLog implements Log
{
    /**
     * @var resource
     */
    private $stream;

    /**
     * @param resource $stream
     */
    public function __construct($stream)
    {
        $this->stream = $stream;
    }

    public function write(LogEntry $entry): void
    {
        fwrite(
            $this->stream,
            sprintf(
                "[%s] %s\n",
                $entry->level()->string(),
                $entry->message()->string()
            )
        );
    }
}
