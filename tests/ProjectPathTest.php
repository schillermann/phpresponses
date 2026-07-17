<?php

declare(strict_types=1);

namespace PhpResponse;

use PHPUnit\Framework\TestCase;

final class ProjectPathTest extends TestCase
{
    public function testResolvesPathRelativeToProjectRoot(): void
    {
        $path = new ProjectPath('tests/ProjectPathTest.php');
        $this->assertSame(
            __FILE__,
            $path->string()
        );
    }
}
