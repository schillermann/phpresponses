<?php

declare(strict_types=1);

namespace PhpResponse\Response\StatusLine;

use PhpResponse\Response\Response;
use PhpResponse\Response\Media;

final class BadRequest implements Response
{
    private Response $origin;

    public function __construct(Response $origin)
    {
        $this->origin = new StatusLine($origin, 400, 'Bad Request');
    }

    public function media(Media $media): Media
    {
        return $this->origin->media($media);
    }
}
