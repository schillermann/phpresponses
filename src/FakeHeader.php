<?php

declare(strict_types=1);

namespace PhpResponses;

final class FakeHeader implements Header
{
    private ?string $value;

    public function __construct(?string $value)
    {
        $this->value = $value;
    }

    public function exists(): bool
    {
        return $this->value !== null;
    }

    public function string(): string
    {
        if ($this->value === null) {
            throw new \Exception("Header not found");
        }
        return $this->value;
    }
}
