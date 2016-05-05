<?php

declare(strict_types=1);

namespace Rusty\Executor;

use Symfony\Component\Process\Process;

use Rusty\CodeSample;
use Rusty\CodeSampleCompiler;
use Rusty\ExecutionContext;

class ExternalProcess implements Executor
{
    private $compiler;

    public function __construct()
    {
        $this->compiler = new CodeSampleCompiler();
    }

    public function execute(CodeSample $sample, ExecutionContext $context): Result
    {
        $code = $this->compiler->compile($sample, $context);

        $tmpFile = tempnam(sys_get_temp_dir(), 'rusty_');
        file_put_contents($tmpFile, $code);

        $process = new Process(sprintf('%s %s', $context->getPhpExecutable(), $tmpFile));
        $process->run();

        unlink($tmpFile);

        return new Result($process->isSuccessful(), $process->getOutput(), $process->getErrorOutput());
    }
}
