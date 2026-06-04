<?php

declare(strict_types=1);

namespace PhpResponses;

interface Request
{
    public function requestLine(): RequestLine;
    public function header(string $name): Header;
    /** @return resource */
    public function body();
}
