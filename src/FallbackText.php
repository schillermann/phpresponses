<?php

declare(strict_types=1);

namespace PhpResponse;

final class FallbackText implements Text {
    
    private Text $origin;
    private Text $fallback;
    private string $exception;

    public function __construct(Text $origin, Text $fallback, string $exception = \Throwable::class) {
        $this->origin = $origin;
        $this->fallback = $fallback;
        $this->exception = $exception;
    }

    public function string(): string {
        try {
            return $this->origin->string();
        } catch (\Throwable $e) {
            if ($e instanceof $this->exception) {
                return $this->fallback->string();
            }
            throw $e;
        }
    }
}
