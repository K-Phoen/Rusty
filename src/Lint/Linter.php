<?php

declare(strict_types=1);

namespace Rusty\Lint;

use Rusty\CodeSample;
use Rusty\ExecutionContext;

interface Linter
{
    function lint(CodeSample $sample, ExecutionContext $context);
}
