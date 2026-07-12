<?php

declare(strict_types=1);

namespace PhpResponse;

interface Response
{
    public function media(Media $media): Media;
}
