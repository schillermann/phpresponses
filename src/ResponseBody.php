<?php

declare(strict_types=1);

namespace PhpResponses;

final class ResponseBody implements Response
{
    private Text $content;

    public function __construct(Text $content)
    {
        $this->content = $content;
    }

    public function media(Media $media): Media
    {
        return $media->body($this->content);
    }
}
