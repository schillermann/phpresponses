<?php

declare(strict_types=1);

namespace PhpResponse;

final class JsonProperty implements Node
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
        return (string) $data[$this->key];
    }

    public function node(string $key): Node
    {
        // We decorate the origin, adding a new layer of navigation
        return new JsonProperty(
            new JsonValue($this->string()), 
            $key
        );
    }
}
