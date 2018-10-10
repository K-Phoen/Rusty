<?php

namespace Rusty\Executor\Exception;

use Rusty\CodeSample;

class ExecutionError extends \RuntimeException
{
    public static function inCodeSample(CodeSample $sample, string $error): self
    {
        return new static(sprintf("Got an error while executing \"%s\" defined in %s:%d\n%s", $sample->getCode(), $sample->getFile()->getRealPath(), $sample->getLine(), $error));
    }
}
