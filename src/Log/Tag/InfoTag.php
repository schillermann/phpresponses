<?php

declare(strict_types=1);

namespace PhpResponse\Log\Tag;

use PhpResponse\Text;

final class InfoTag implements Text
{
    public function string(): string
    {
        return 'INFO';
    }
}
