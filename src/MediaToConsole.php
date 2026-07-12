<?php

declare(strict_types=1);

namespace PhpResponse;

final class MediaToConsole implements Media
{
    public function status(int $code, string $message): Media
    {
        echo "Status: {$code} {$message}\n";
        return new self();
    }

    public function header(string $name, string $value): Media
    {
        echo "{$name}: {$value}\n";
        return new self();
    }

    public function body(Text $content): Media
    {
        echo "\n{$content->string()}";
        return new self();
    }
}
