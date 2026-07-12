<?php

declare(strict_types=1);

namespace PhpResponse;

final class TemplateVariable implements Text {
    
    private Text $origin;
    private string $name;
    private Text $value;

    public function __construct(Text $origin, string $name, Text $value) {
        $this->origin = $origin;
        $this->name = $name;
        $this->value = $value;
    }

    public function string(): string {
        return str_replace(
            '${' . $this->name . '}',
            $this->value->string(),
            $this->origin->string()
        );
    }
}
