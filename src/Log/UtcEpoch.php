<?php

declare(strict_types=1);

namespace PhpResponse\Log;

final class UtcEpoch implements Epoch
{
    public function string(): string
    {
        return gmdate('c');
    }
}
