<?php

declare(strict_types=1);

namespace PhpResponse\Text\Json;

use PhpResponse\Text;
use PhpResponse\Text\LiteralText;

final class JsonMember implements Text
{
    private string $key;
    private Text $value;

    public function __construct(string $key, Json|Text|string $value)
    {
        $this->key = $key;
        $this->value = is_string($value) ? new JsonText(new LiteralText($value)) : $value;
    }

    public function string(): string
    {
        return json_encode($this->key, JSON_THROW_ON_ERROR) . ':' . $this->value->string();
    }
}
