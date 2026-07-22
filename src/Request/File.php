<?php

declare(strict_types=1);

namespace PhpResponse\Request;

use PhpResponse\Text;

interface File
{
    public function saveTo(Text $destination): void;
}
