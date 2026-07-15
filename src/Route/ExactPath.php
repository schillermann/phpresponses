<?php

declare(strict_types=1);

namespace PhpResponse\Route;

use PhpResponse\Request\Path as RequestPath;
use PhpResponse\Response\Response;
use PhpResponse\Response\Media;
use PhpResponse\Text;

final class ExactPath implements Response
{
    private Text $path;
    private string $expected;
    private Response $target;
    private Response $fallback;

    public function __construct(
        string $expected,
        Response $target,
        Response $fallback,
        Text $path = new RequestPath()
    ) {
        $this->expected = $expected;
        $this->target = $target;
        $this->fallback = $fallback;
        $this->path = $path;
    }

    public function media(Media $media): Media
    {
        if ($this->path->string() === $this->expected) {
            return $this->target->media($media);
        }
        return $this->fallback->media($media);
    }
}
