<?php

declare(strict_types=1);

namespace PhpResponse\Text\Json;

use PhpResponse\Text;

final class JsonObject implements Json
{
    /** @var array<JsonMember> */
    private array $members;

    public function __construct(JsonMember ...$members)
    {
        $this->members = $members;
    }

    public function string(): string
    {
        return '{' . implode(',', array_map(fn(JsonMember $m) => $m->string(), $this->members)) . '}';
    }
}
