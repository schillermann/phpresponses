<?php

declare(strict_types=1);

namespace PhpResponse;

final class FakeMedia implements Media
{
    private array $log;

    public function __construct(array $log = [])
    {
        $this->log = $log;
    }

    public function status(int $code, string $message): Media
    {
        $newLog = $this->log;
        $newLog[] = "status: $code $message";
        return new self($newLog);
    }

    public function header(string $name, string $value): Media
    {
        $newLog = $this->log;
        $newLog[] = "header: $name=$value";
        return new self($newLog);
    }

    public function body(Text $content): Media
    {
        $newLog = $this->log;
        $newLog[] = "body: " . $content->string();
        return new self($newLog);
    }

    public function array(): array
    {
        return $this->log;
    }
}
