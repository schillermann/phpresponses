<?php

declare(strict_types=1);

namespace PhpResponse\Response;

use PhpResponse\Response\Response;
use PhpResponse\Response\Media;
use PhpResponse\Text;

final class Redirect implements Response
{
    private Text $target;
    private int $code;

    public function __construct(Text $target, int $code = 302)
    {
        $this->target = $target;
        $this->code = $code;
    }

    public function media(Media $media): Media
    {
        $messages = [
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
        ];
        $message = $messages[$this->code] ?? 'Redirect';
        return $media
            ->status($this->code, $message)
            ->header('Location', $this->target->string());
    }
}
