<?php

declare(strict_types=1);

namespace PhpResponse\Log\Tag;

use PhpResponse\Text;

final class ErrorTag implements Text
{
    public function string(): string
    {
        return 'ERROR';
    }
}
