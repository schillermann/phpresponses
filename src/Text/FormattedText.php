<?php

declare(strict_types=1);

namespace PhpResponse\Text;

use PhpResponse\Text;

final class FormattedText implements Text {
    
    private Text $template;
    
    /** @var array<int, Text> */
    private array $args;

    public function __construct(Text $template, Text ...$args) {
        $this->template = $template;
        $this->args = $args;
    }

    public function string(): string {
        $resolved = array_map(
            fn(Text $arg) => $arg->string(), 
            $this->args
        );

        return sprintf($this->template->string(), ...$resolved);
    }
}
