<?php

declare(strict_types=1);

namespace PhpResponse;

use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use PhpResponse\Request\PathParam;
use PhpResponse\Text\LiteralText;

final class PathParamTest extends TestCase
{
    public function testExtractsNamedGroupFromPath(): void
    {
        $param = new PathParam(
            '#^/users/(?<id>\d+)$#',
            'id',
            new LiteralText('/users/42')
        );

        $this->assertEquals('42', $param->string());
    }

    public function testExtractsNumericGroupFromPath(): void
    {
        $param = new PathParam(
            '#^/orders/(\d+)/items/(\w+)$#',
            2,
            new LiteralText('/orders/101/items/book')
        );

        $this->assertEquals('book', $param->string());
    }

    public function testThrowsExceptionWhenPathDoesNotMatch(): void
    {
        $param = new PathParam(
            '#^/users/(?<id>\d+)$#',
            'id',
            new LiteralText('/users/abc')
        );

        $this->expectException(OutOfBoundsException::class);
        $param->string();
    }

    public function testThrowsExceptionWhenGroupMissing(): void
    {
        $param = new PathParam(
            '#^/users/(\d+)$#',
            'missing_group',
            new LiteralText('/users/42')
        );

        $this->expectException(OutOfBoundsException::class);
        $param->string();
    }
}
