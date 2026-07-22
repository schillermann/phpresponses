<?php

declare(strict_types=1);

namespace PhpResponse\Request;

use OutOfBoundsException;
use PhpResponse\Text;

final class FormParam implements Text
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function string(): string
    {
        if (!isset($_POST[$this->name])) {
            throw new OutOfBoundsException("Form parameter '{$this->name}' is missing.");
        }

        $val = $_POST[$this->name];
        if (is_array($val)) {
            return implode(',', array_map('strval', $val));
        }

        return (string) $val;
    }
}
