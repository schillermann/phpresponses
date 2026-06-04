<?php

declare(strict_types=1);

namespace PhpResponses;

final class RequestFromEnv implements Request
{
    private array $server;

    public function __construct(array $server = null)
    {
        $this->server = $server ?? $_SERVER;
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
        return fopen('php://input', 'rb');
    }
}
