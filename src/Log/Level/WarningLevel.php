<?php

declare(strict_types=1);

namespace PhpResponse\Log\Level;

final class WarningLevel implements LogLevel
{
    public function string(): string
    {
        return 'WARNING';
    }
}
