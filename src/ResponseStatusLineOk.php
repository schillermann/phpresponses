<?php

declare(strict_types=1);

namespace PhpResponse;

final class ResponseStatusLineOk implements Response
{
    private Response $origin;

    public function __construct(Response $origin)
    {
        $this->origin = new ResponseStatusLine($origin, 200, 'OK');
    }

    public function media(Media $media): Media
    {
        return $this->origin->media($media);
    }
}
