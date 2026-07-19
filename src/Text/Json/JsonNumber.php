<?php

declare(strict_types=1);

namespace PhpResponse\Text\Json;

final class JsonNumber implements Json
{
    private int|float $value;

    public function __construct(int|float $value)
    {
        $this->value = $value;
    }

    public function string(): string
    {
        return (string) $this->value;
    }
}
