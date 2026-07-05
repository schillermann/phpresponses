<?php

declare(strict_types=1);

namespace PhpResponses;

final class MediaToWire implements Media
{
    public function status(int $code, string $message): Media
    {
        http_response_code($code);
        return new self();
    }

    public function header(string $name, string $value): Media
    {
        header("{$name}: {$value}");
        return new self();
    }

    public function body(Text $content): Media
    {
        echo $content->string();
        return new self();
    }
}
