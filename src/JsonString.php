<?php

declare(strict_types=1);

namespace PhpResponses;

final class JsonString implements Text 
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
        if (!isset($data[$this->key]) || !is_string($data[$this->key])) {
            throw new \DomainException("Bad Request: '{$this->key}' is not a string.");
        }
        return $data[$this->key];
    }
}
