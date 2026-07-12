<?php

declare(strict_types=1);

namespace PhpResponse;

final class StickyText implements Text
{
    private Text $origin;
    private string $cached;
    private bool $read;

    public function __construct(Text $origin)
    {
        $this->origin = $origin;
        $this->cached = '';
        $this->read = false;
    }

    public function string(): string
    {
        if (!$this->read) {
            $this->cached = $this->origin->string();
            $this->read = true;
        }
        return $this->cached;
    }
}
