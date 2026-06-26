<?php

declare(strict_types=1);

namespace PhpResponses;

final class BodyTextFromRequestFake implements Text
{
    private string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function string(): string
    {
        return $this->content;
    }
}
