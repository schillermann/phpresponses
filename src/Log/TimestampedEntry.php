<?php

declare(strict_types=1);

namespace PhpResponse\Log;

use PhpResponse\Text;
use PhpResponse\FormattedText;
use PhpResponse\LiteralText;

final class TimestampedEntry implements LogEntry
{
    private LogEntry $origin;
    private Epoch $epoch;

    public function __construct(LogEntry $origin, Epoch $epoch)
    {
        $this->origin = $origin;
        $this->epoch = $epoch;
    }

    public function level(): Text
    {
        return $this->origin->level();
    }

    public function message(): Text
    {
        return new FormattedText(
            new LiteralText("[%s] %s"),
            $this->epoch,
            $this->origin->message()
        );
    }
}
