<?php

declare(strict_types=1);

namespace PhpResponse\Text\Json;

use PhpResponse\Text;
use PhpResponse\Text\LiteralText;

final class JsonText implements Json
{
    private Text $origin;

    public function __construct(Text|string $origin)
    {
        $this->origin = is_string($origin) ? new LiteralText($origin) : $origin;
    }

    public function string(): string
    {
        return json_encode($this->origin->string(), JSON_THROW_ON_ERROR);
    }
}
