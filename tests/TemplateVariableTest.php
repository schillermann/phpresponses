<?php

declare(strict_types=1);

namespace PhpResponse;
use PhpResponse\Text\LiteralText;
use PhpResponse\Text\TemplateVariable;

use PHPUnit\Framework\TestCase;

final class TemplateVariableTest extends TestCase
{
    public function testRendersTemplateVariables(): void
    {
        $rendered = new TemplateVariable(
            new TemplateVariable(
                new LiteralText('Hello, ${name}! You are ${age} years old.'),
                'name',
                new LiteralText('Yegor')
            ),
            'age',
            new LiteralText('40')
        );

        $this->assertEquals('Hello, Yegor! You are 40 years old.', $rendered->string());
    }
}
