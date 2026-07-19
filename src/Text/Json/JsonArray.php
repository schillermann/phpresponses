<?php

declare(strict_types=1);

namespace PhpResponse\Text\Json;

use PhpResponse\Text;
use PhpResponse\Text\LiteralText;

final class JsonArray implements Json
{
    /** @var array<Text> */
    private array $items;

    public function __construct(Json|Text|string ...$items)
    {
        $this->items = array_map(
            fn($item) => is_string($item) ? new JsonText(new LiteralText($item)) : $item,
            $items
        );
    }

    public function string(): string
    {
        return '[' . implode(',', array_map(fn(Text $t) => $t->string(), $this->items)) . ']';
    }
}
