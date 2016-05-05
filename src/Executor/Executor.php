<?php

namespace Rusty\Executor;

use Rusty\CodeSample;
use Rusty\ExecutionContext;

interface Executor
{
    function execute(CodeSample $sample, ExecutionContext $context): Result;
}
