<?php

declare(strict_types=1);

namespace PhpResponses;

final class FakeRequestLine implements RequestLine
{
    private string $method;
    private string $path;
    private string $query;
    private string $protocol;

    public function __construct(
        string $method = 'GET',
        string $path = '/',
        string $query = '',
        string $protocol = 'HTTP/1.1'
    ) {
        $this->method = $method;
        $this->path = $path;
        $this->query = $query;
        $this->protocol = $protocol;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(): string
    {
        return $this->query;
    }

    public function protocol(): string
    {
        return $this->protocol;
    }
}
