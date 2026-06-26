<?php

declare(strict_types=1);

namespace PhpResponses;

final class TextOfStream implements Text
{
    /**
     * @var resource
     */
    private $stream;

    /**
     * @param resource $stream
     */
    public function __construct($stream)
    {
        $this->stream = $stream;
    }

    public function string(): string
    {
        $content = stream_get_contents($this->stream);
        if ($content === false) {
            throw new BodyReadException("Could not read stream");
        }
        return (string) $content;
    }
}
