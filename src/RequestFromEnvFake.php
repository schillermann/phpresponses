<?php

declare(strict_types=1);

namespace PhpResponses;

final class RequestFromEnvFake implements Request
{
    private array $server;
    private string $body;

    public function __construct(array $server = [], string $body = "")
    {
        $this->server = $server;
        $this->body = $body;
    }

    public function requestLine(): RequestLine
    {
        return new RequestLineFromEnv($this->server);
    }

    public function header(string $name): Header
    {
        return new HeaderFromEnv($name, $this->server);
    }

    public function body()
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $this->body);
        rewind($stream);
        return $stream;
    }
}
