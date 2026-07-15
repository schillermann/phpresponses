<?php

declare(strict_types=1);

namespace PhpResponse\Route;

use PhpResponse\Request\Path;
use PhpResponse\Response\Response;
use PhpResponse\Response\Media;
use PhpResponse\Text;

final class RegexPath implements Response
{
    private Text $path;
    private string $pattern;
    private Response $target;
    private Response $fallback;

    public function __construct(
        string $pattern,
        Response $target,
        Response $fallback,
        Text $path = new Path()
    ) {
        $this->pattern = $pattern;
        $this->target = $target;
        $this->fallback = $fallback;
        $this->path = $path;
    }

    public function media(Media $media): Media
    {
        if (preg_match($this->pattern, $this->path->string()) === 1) {
            return $this->target->media($media);
        }
        return $this->fallback->media($media);
    }
}
