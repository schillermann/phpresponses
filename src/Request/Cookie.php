<?php

declare(strict_types=1);

namespace PhpResponse\Request;

use OutOfBoundsException;
use PhpResponse\Text;

final class Cookie implements Text
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function string(): string
    {
        if (!isset($_COOKIE[$this->name])) {
            throw new OutOfBoundsException("Cookie '{$this->name}' is missing.");
        }

        $val = $_COOKIE[$this->name];
        if (is_array($val)) {
            return implode(',', array_map('strval', $val));
        }

        return (string) $val;
    }
}
