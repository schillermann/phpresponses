<?php

declare(strict_types=1);

namespace PhpResponses;

final class BodyFromRequest
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function string(): string
    {
        $stream = $this->request->body();
        $content = stream_get_contents($stream);
        if ($content === false) {
            throw new BodyReadException("Could not read request body");
        }
        return (string) $content;
    }
}
