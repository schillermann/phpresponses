<?php

declare(strict_types=1);

namespace PhpResponses;

interface Header
{
    public function exists(): bool;
    public function string(): string;
}
