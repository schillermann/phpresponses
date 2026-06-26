<?php

declare(strict_types=1);

namespace PhpResponses;

use PHPUnit\Framework\TestCase;

if (!function_exists('PhpResponses\stream_get_contents')) {
    /**
     * Override the built-in stream_get_contents for testing failure scenarios.
     *
     * @param resource $stream
     * @param int|null $maxlength
     * @param int|null $offset
     * @return string|false
     */
    function stream_get_contents($stream, $maxlength = null, $offset = null)
    {
        if (isset($GLOBALS['mock_stream_get_contents_fail']) && $GLOBALS['mock_stream_get_contents_fail']) {
            return false;
        }
        return \stream_get_contents($stream, $maxlength ?? -1, $offset ?? -1);
    }
}

final class BodyTextFromRequestTest extends TestCase
{
    protected function tearDown(): void
    {
        $GLOBALS['mock_stream_get_contents_fail'] = false;
        parent::tearDown();
    }

    public function testReadsBodySuccessfully(): void
    {
        $expected = "test request body content";
        $request = new RequestFromEnvFake([], $expected);
        $body = new BodyTextFromRequest($request);

        $this->assertSame($expected, $body->string());
    }

    public function testThrowsExceptionOnReadFailure(): void
    {
        $request = new RequestFromEnvFake([], "some content");
        $body = new BodyTextFromRequest($request);

        $GLOBALS['mock_stream_get_contents_fail'] = true;

        $this->expectException(BodyReadException::class);
        $this->expectExceptionMessage("Could not read request body");

        $body->string();
    }
}
