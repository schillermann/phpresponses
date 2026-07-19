<?php

declare(strict_types=1);

namespace PhpResponse\Text\Json;

final class JsonTrue implements Json
{
    public function string(): string
    {
        return 'true';
    }
}
