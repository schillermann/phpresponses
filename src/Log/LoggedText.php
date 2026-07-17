<?php

declare(strict_types=1);

namespace PhpResponse\Log;

use PhpResponse\Text;
use PhpResponse\LiteralText;
use PhpResponse\FormattedText;
use PhpResponse\Log\Tag\DebugTag;

final class LoggedText implements Text
{
    private Text $origin;
    private Log $log;

    public function __construct(Text $origin, Log $log)
    {
        $this->origin = $origin;
        $this->log = $log;
    }

    public function string(): string
    {
        $result = $this->origin->string();
        $this->log->write(
            new PlainEntry(
                new DebugTag(),
                new FormattedText(
                    new LiteralText("Evaluated text: '%s'"),
                    new LiteralText($result)
                )
            )
        );
        return $result;
    }
}
