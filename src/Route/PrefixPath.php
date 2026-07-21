<?php

declare(strict_types=1);

namespace PhpResponse\Route;

use PhpResponse\Request\Path as RequestPath;
use PhpResponse\Response\Response;
use PhpResponse\Response\Media;
use PhpResponse\Text;

final class PrefixPath implements Response
{
    private string $prefix;
    private Response $target;
    private Response $fallback;
    private Text $path;

    public function __construct(
        string $prefix,
        Response $target,
        Response $fallback,
        Text $path = new RequestPath()
    ) {
        $this->prefix = $prefix;
        $this->target = $target;
        $this->fallback = $fallback;
        $this->path = $path;
    }

    public function media(Media $media): Media
    {
        if (str_starts_with($this->path->string(), $this->prefix)) {
            return $this->target->media($media);
        }
        return $this->fallback->media($media);
    }
}
