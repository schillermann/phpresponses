<?php

declare(strict_types=1);

namespace PhpResponses;

final class HeaderFromEnv implements Header
{
    private string $name;
    private array $server;

    public function __construct(string $name, array $server)
    {
        $this->name = $name;
        $this->server = $server;
    }

    public function exists(): bool
    {
        return isset($this->server[$this->key()]);
    }

    public function string(): string
    {
        if (!$this->exists()) {
            throw new HttpHeaderNotFoundException($this->name);
        }
        return $this->server[$this->key()];
    }

    private function key(): string
    {
        return 'HTTP_' . str_replace('-', '_', strtoupper($this->name));
    }
}
