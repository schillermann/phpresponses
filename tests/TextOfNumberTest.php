<?php

declare(strict_types=1);

namespace PhpResponse;

use PHPUnit\Framework\TestCase;

final class TextOfNumberTest extends TestCase
{
    public function testConvertsNumberToText(): void
    {
        $number = new class implements Number {
            public function int(): int {
                return 42;
            }
        };

        $this->assertEquals("42", (new TextOfNumber($number))->string());
    }
}
