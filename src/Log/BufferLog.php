<?php

declare(strict_types=1);

namespace PhpResponse\Log;

final class BufferLog implements Log
{
    private \ArrayObject $buffer;

    public function __construct(\ArrayObject $buffer)
    {
        $this->buffer = $buffer;
    }

    public function write(LogEntry $entry): void
    {
        $this->buffer->append(
            sprintf(
                '%s: %s',
                $entry->level()->string(),
                $entry->message()->string()
            )
        );
    }
}
