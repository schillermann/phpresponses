<?php

declare(strict_types=1);

namespace PhpResponse\Log\Level;

final class ErrorLevel implements LogLevel
{
    public function string(): string
    {
        return 'ERROR';
    }
}
