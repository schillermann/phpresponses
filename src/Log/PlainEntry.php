<?php

declare(strict_types=1);

namespace PhpResponse\Log;

use PhpResponse\Text;

final class PlainEntry implements LogEntry
{
    private Text $level;
    private Text $message;

    public function __construct(Text $level, Text $message)
    {
        $this->level = $level;
        $this->message = $message;
    }

    public function level(): Text
    {
        return $this->level;
    }

    public function message(): Text
    {
        return $this->message;
    }
}
