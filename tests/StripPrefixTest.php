<?php

declare(strict_types=1);

namespace PhpResponse;

use PHPUnit\Framework\TestCase;
use PhpResponse\Text\LiteralText;
use PhpResponse\Text\StripPrefix;

final class StripPrefixTest extends TestCase
{
    public function testStripsPrefixWhenMatching(): void
    {
        $stripped = new StripPrefix(
            '/api/v1',
            new LiteralText('/api/v1/users/42')
        );

        $this->assertEquals('/users/42', $stripped->string());
    }

    public function testReturnsOriginalPathWhenPrefixDoesNotMatch(): void
    {
        $stripped = new StripPrefix(
            '/api/v1',
            new LiteralText('/web/dashboard')
        );

        $this->assertEquals('/web/dashboard', $stripped->string());
    }
}
