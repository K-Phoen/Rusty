<?php

declare(strict_types=1);

namespace Rusty\Lint;

use Rusty\CodeSample;
use Rusty\ExecutionContext;

interface Linter
{
    /**
     * Lints code samples.
     *
     * @throws Exception\SyntaxError If the code sample has a syntax error.
     *
     * @return bool True if the code sample has a valid syntax.
     */
    function lint(CodeSample $sample, ExecutionContext $context): bool;
}
