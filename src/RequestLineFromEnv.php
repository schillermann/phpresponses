<?php

declare(strict_types=1);

namespace PhpResponses;

final class RequestLineFromEnv implements RequestLine
{
    private array $server;

    public function __construct(array $server)
    {
        $this->server = $server;
    }

    public function method(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function path(): string
    {
        return parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    }

    public function query(): string
    {
        return $this->server['QUERY_STRING'] ?? '';
    }

    public function protocol(): string
    {
        return $this->server['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
    }
}
