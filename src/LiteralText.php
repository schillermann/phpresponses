<?php

declare(strict_types=1);

namespace PhpResponses;

final class LiteralText implements Text {
    private string $text;

    public function __construct(string $text) {
        $this->text = $text;
    }

    public function string(): string {
        return $this->text;
    }
}
