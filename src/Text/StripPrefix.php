<?php

declare(strict_types=1);

namespace PhpResponse\Text;

use PhpResponse\Request\Path;
use PhpResponse\Text;

final class StripPrefix implements Text
{
    private string $prefix;
    private Text $origin;

    public function __construct(string $prefix, Text $origin = new Path())
    {
        $this->prefix = $prefix;
        $this->origin = $origin;
    }

    public function string(): string
    {
        $full = $this->origin->string();
        if (str_starts_with($full, $this->prefix)) {
            return substr($full, strlen($this->prefix));
        }
        return $full;
    }
}
