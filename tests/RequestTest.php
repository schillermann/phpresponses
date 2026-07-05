<?php

declare(strict_types=1);

namespace PhpResponses;

use PHPUnit\Framework\TestCase;
use PhpResponses\Request\BodyFromEnv;
use PhpResponses\Request\HeaderFromEnv;
use PhpResponses\Request\MethodFromEnv;
use PhpResponses\Request\PathFromEnv;
use PhpResponses\Request\ProtocolFromEnv;
use PhpResponses\Request\QueryParamFromEnv;

final class RequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Clear superglobals to ensure test isolation
        $_SERVER = [];
        $_GET = [];
    }

    public function testHeaderFromEnv(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
        $_SERVER['CONTENT_TYPE'] = 'application/json';

        $this->assertEquals('Mozilla/5.0', (new HeaderFromEnv('User-Agent'))->string());
        $this->assertEquals('application/json', (new HeaderFromEnv('Content-Type'))->string());
    }

    public function testHeaderFromEnvThrowsExceptionWhenMissing(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        (new HeaderFromEnv('User-Agent'))->string();
    }

    public function testMethodFromEnv(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertEquals('POST', (new MethodFromEnv())->string());
    }

    public function testMethodFromEnvDefaultsToGet(): void
    {
        $this->assertEquals('GET', (new MethodFromEnv())->string());
    }

    public function testPathFromEnv(): void
    {
        $_SERVER['REQUEST_URI'] = '/some/path?query=1';
        $this->assertEquals('/some/path', (new PathFromEnv())->string());
    }

    public function testPathFromEnvDefaultsToSlash(): void
    {
        $this->assertEquals('/', (new PathFromEnv())->string());
    }

    public function testProtocolFromEnv(): void
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/2.0';
        $this->assertEquals('HTTP/2.0', (new ProtocolFromEnv())->string());
    }

    public function testProtocolFromEnvDefaults(): void
    {
        $this->assertEquals('HTTP/1.1', (new ProtocolFromEnv())->string());
    }

    public function testQueryParamFromEnv(): void
    {
        $_GET['name'] = 'yegor';
        $this->assertEquals('yegor', (new QueryParamFromEnv('name'))->string());
    }

    public function testQueryParamFromEnvThrowsExceptionWhenMissing(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        (new QueryParamFromEnv('name'))->string();
    }

    public function testBodyFromEnv(): void
    {
        $this->assertIsString((new BodyFromEnv())->string());
    }
}
