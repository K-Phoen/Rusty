<?php

namespace Rusty\Executor;

use Rusty\CodeSample;
use Rusty\ExecutionContext;

interface Executor
{
    public function execute(CodeSample $sample, ExecutionContext $context): Result;
}
