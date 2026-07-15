<?php

declare(strict_types=1);

namespace PhpResponse\Route;

use PhpResponse\Request\Method as RequestMethod;
use PhpResponse\Response\Response;
use PhpResponse\Response\Media;
use PhpResponse\Text;

final class Method implements Response
{
    private Text $method;
    private string $expected;
    private Response $target;
    private Response $fallback;

    public function __construct(
        string $expected,
        Response $target,
        Response $fallback,
        Text $method = new RequestMethod()
    ) {
        $this->expected = $expected;
        $this->target = $target;
        $this->fallback = $fallback;
        $this->method = $method;
    }

    public function media(Media $media): Media
    {
        if (strcasecmp($this->method->string(), $this->expected) === 0) {
            return $this->target->media($media);
        }
        return $this->fallback->media($media);
    }
}
