<?php

declare(strict_types=1);

namespace Rusty;

class CodeSample
{
    private $file;
    private $line;
    private $code;
    private $pragmaDirectives = [];

    public function __construct(\SplFileInfo $file, int $line, string $code, array $pragmaDirectives = [])
    {
        $this->file = $file;
        $this->line = $line;
        $this->code = trim(strpos($code, '<?php') === 0 ? substr($code, 5) : $code);
        $this->pragmaDirectives = $pragmaDirectives;
    }

    public function hasPragma(string $pragma): bool
    {
        return in_array($pragma, $this->pragmaDirectives, true);
    }

    public function getPragmaDirectives(): array
    {
        return $this->pragmaDirectives;
    }

    public function getFile(): \SplFileInfo
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
