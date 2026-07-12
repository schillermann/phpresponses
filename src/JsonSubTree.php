<?php

declare(strict_types=1);

namespace PhpResponse;

final class JsonSubTree implements Text 
{
    private Text $origin;
    private string $key;

    public function __construct(Text $origin, string $key) 
    {
        $this->origin = $origin;
        $this->key = $key;
    }

    public function string(): string 
    {
        $data = json_decode($this->origin->string(), true, 512, JSON_THROW_ON_ERROR);
        if (!isset($data[$this->key]) || !is_array($data[$this->key])) {
            throw new \InvalidArgumentException("Bad Request: '{$this->key}' is missing or not a node.");
        }
        return json_encode($data[$this->key], JSON_THROW_ON_ERROR);
    }
}
