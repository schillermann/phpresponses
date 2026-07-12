<?php

declare(strict_types=1);

namespace PhpResponse;

final class ResponseHeader implements Response
{
    private Response $origin;
    private string $name;
    private string $value;

    public function __construct(Response $origin, string $name, string $value)
    {
        $this->origin = $origin;
        $this->name = $name;
        $this->value = $value;
    }

    public function media(Media $media): Media
    {
        return $this->origin->media(
            $media->header($this->name, $this->value)
        );
    }
}
