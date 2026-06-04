<?php

declare(strict_types=1);

namespace PhpResponses;

final class FakeRequest implements Request
{
    private array $headers;
    private string $body;

    private RequestLine $line;

    public function __construct(array $headers = [], string $body = "", RequestLine $line = null)
    {
        $this->headers = $headers;
        $this->body = $body;
        $this->line = $line ?? new FakeRequestLine();
    }

    public function requestLine(): RequestLine
    {
        return $this->line;
    }

    public function header(string $name): Header
    {
        return new FakeHeader($this->headers[$name] ?? null);
    }

    public function body()
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $this->body);
        rewind($stream);
        return $stream;
    }
}
