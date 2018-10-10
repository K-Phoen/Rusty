<?php

namespace Rusty\Lint\Exception;

use Rusty\CodeSample;

class SyntaxError extends \Exception
{
    public static function inCodeSample(CodeSample $sample, string $error): self
    {
        return new static(sprintf('Syntax error in code sample "%s" in %s:%d → %s', $sample->getCode(), $sample->getFile()->getRealPath(), $sample->getLine(), $error));
    }
}
