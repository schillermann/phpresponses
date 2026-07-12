<?php

declare(strict_types=1);

namespace PhpResponse;

final class ResponseStatusLine implements Response
{
    private Response $origin;
    private int $code;
    private string $message;

    public function __construct(Response $origin, int $code, string $message)
    {
        $this->origin = $origin;
        $this->code = $code;
        $this->message = $message;
    }

    public function media(Media $media): Media
    {
        return $this->origin->media(
            $media->status($this->code, $this->message)
        );
    }
}
