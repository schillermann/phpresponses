<?php

declare(strict_types=1);

namespace PhpResponse\Text;

use PhpResponse\Text;
use PhpResponse\Number;

final class TextOfNumber implements Text {
    
    private Number $number;

    public function __construct(Number $number) {
        $this->number = $number;
    }

    public function string(): string {
        return (string) $this->number->int();
    }
}
