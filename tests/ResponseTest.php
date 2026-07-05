<?php

declare(strict_types=1);

namespace PhpResponses;

use PHPUnit\Framework\TestCase;

final class ResponseTest extends TestCase
{
    public function testFullResponse(): void
    {
        $media = (new ResponseStatusLineOk(
            new ResponseHeader(
                new ResponseBody(new LiteralText("Hello!")),
                "X-Custom", "Value"
            )
        ))->media(new FakeMedia());

        /** @var FakeMedia $media */
        $this->assertEquals(
            [
                "status: 200 OK",
                "header: X-Custom=Value",
                "body: Hello!"
            ],
            $media->array()
        );
    }
}
