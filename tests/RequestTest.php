<?php

declare(strict_types=1);

namespace PhpResponses;

use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    public function testRequestData(): void
    {
        $request = new RequestFromEnvFake(
            ["HTTP_USER_AGENT" => "Mozilla/5.0"],
            "test body"
        );
        
        $this->assertTrue($request->header("User-Agent")->exists());
        $this->assertEquals("Mozilla/5.0", $request->header("User-Agent")->string());
        $this->assertEquals("test body", (new BodyTextFromRequest($request))->string());
    }

    public function testLiveHeaderMapping(): void
    {
        $request = new RequestFromEnv([
            "HTTP_USER_AGENT" => "Opera"
        ]);
        $this->assertEquals("Opera", $request->header("User-Agent")->string());
    }

    public function testRequestLine(): void
    {
        $request = new RequestFromEnv([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/save?id=1',
            'QUERY_STRING' => 'id=1',
            'SERVER_PROTOCOL' => 'HTTP/2'
        ]);
        
        $line = $request->requestLine();
        $this->assertEquals('POST', $line->method());
        $this->assertEquals('/save', $line->path());
        $this->assertEquals('id=1', $line->query());
        $this->assertEquals('HTTP/2', $line->protocol());
    }

    public function testRequestFromEnvFakeRequestLine(): void
    {
        $request = new RequestFromEnvFake([
            'REQUEST_METHOD' => 'DELETE',
            'REQUEST_URI' => '/items/123'
        ]);

        $line = $request->requestLine();
        $this->assertEquals('DELETE', $line->method());
        $this->assertEquals('/items/123', $line->path());
    }
}
