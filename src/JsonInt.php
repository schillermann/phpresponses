<?php

declare(strict_types=1);

namespace PhpResponses;

final class JsonInt implements Number 
{
    private Text $origin;
    private string $key;

    public function __construct(Text $origin, string $key) 
    {
        $this->origin = $origin;
        $this->key = $key;
    }

    public function int(): int 
    {
        $data = json_decode($this->origin->string(), true, 512, JSON_THROW_ON_ERROR);
        if (!isset($data[$this->key]) || !is_int($data[$this->key])) {
            throw new \DomainException("Bad Request: '{$this->key}' is not an integer.");
        }
        return $data[$this->key];
    }
}
