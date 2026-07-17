<?php

declare(strict_types=1);

namespace PhpResponse\Log;

use PhpResponse\Text;

interface LogEntry
{
    public function level(): Text;

    public function message(): Text;
}
