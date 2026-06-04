<?php

declare(strict_types=1);

namespace PhpResponses;

interface RequestLine
{
    public function method(): string;
    public function path(): string;
    public function query(): string;
    public function protocol(): string;
}
