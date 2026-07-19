<?php

declare(strict_types=1);

namespace PhpResponse;
use PhpResponse\Text\LiteralText;
use PhpResponse\Text\FallbackText;

use PHPUnit\Framework\TestCase;

final class FallbackTextTest extends TestCase
{
    public function testReturnsOriginWhenSuccessful(): void
    {
        $text = new FallbackText(
            new LiteralText('success'),
            new LiteralText('fallback')
        );

        $this->assertEquals('success', $text->string());
    }

    public function testReturnsFallbackWhenExceptionThrown(): void
    {
        $origin = new class implements Text {
            public function string(): string {
                throw new \RuntimeException('error');
            }
        };

        $text = new FallbackText(
            $origin,
            new LiteralText('fallback')
        );

        $this->assertEquals('fallback', $text->string());
    }

    public function testPropagatesUnmatchedException(): void
    {
        $origin = new class implements Text {
            public function string(): string {
                throw new \InvalidArgumentException('error');
            }
        };

        $text = new FallbackText(
            $origin,
            new LiteralText('fallback'),
            \RuntimeException::class
        );

        $this->expectException(\InvalidArgumentException::class);
        $text->string();
    }
}
