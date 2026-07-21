<?php

declare(strict_types=1);

namespace PhpResponse\Request;

use PhpResponse\Text;

final class PathParam implements Text
{
    private string $pattern;
    private int|string $group;
    private Text $path;

    public function __construct(
        string $pattern,
        int|string $group = 1,
        Text $path = new Path()
    ) {
        $this->pattern = $pattern;
        $this->group = $group;
        $this->path = $path;
    }

    public function string(): string
    {
        $currentPath = $this->path->string();
        if (preg_match($this->pattern, $currentPath, $matches) !== 1) {
            throw new \OutOfBoundsException(
                "Path '{$currentPath}' does not match pattern '{$this->pattern}'."
            );
        }

        if (!isset($matches[$this->group])) {
            throw new \OutOfBoundsException(
                "Group '{$this->group}' missing in path match for pattern '{$this->pattern}'."
            );
        }

        return (string) $matches[$this->group];
    }
}
