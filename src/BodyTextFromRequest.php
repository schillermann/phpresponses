<?php

declare(strict_types=1);

namespace PhpResponses;

final class BodyTextFromRequest implements Text 
{
    private Text $origin;

    public function __construct(Request $request)
    {
        $this->origin = new StickyText(
            new TextOfStream($request->body())
        );
    }

    public function string(): string
    {
        return $this->origin->string();
    }
}
