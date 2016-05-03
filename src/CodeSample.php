<?php

declare(strict_types=1);

namespace Rusty;

class CodeSample
{
    private $file;
    private $line;
    private $code;

    public function __construct(\SplFileInfo $file, int $line, string $code)
    {
        $this->file = $file;
        $this->line = $line;
        $this->code = $code;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
