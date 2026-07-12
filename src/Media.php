<?php

declare(strict_types=1);

namespace PhpResponse;

interface Media
{
    public function status(int $code, string $message): Media;
    public function header(string $name, string $value): Media;
    public function body(Text $content): Media;
}
