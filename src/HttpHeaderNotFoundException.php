<?php

declare(strict_types=1);

namespace PhpResponses;

final class HttpHeaderNotFoundException extends \Exception
{
    public function __construct(string $name)
    {
        parent::__construct("Header '{$name}' not found");
    }
}
