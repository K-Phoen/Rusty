<?php

declare(strict_types=1);

namespace Rusty;

use Symfony\Component\Process\PhpExecutableFinder;

use Rusty\Finder\PHPFilesFinder;

class ExecutionContext
{
    private $target;
    private $disableLint = false;
    private $disableExecute = false;
    private $stopOnError = false;
    private $bootstrapFiles = [];

    public function __construct(string $target, array $bootstrapFiles = [])
    {
        $this->target = $target;
        $this->bootstrapFiles = $bootstrapFiles;
    }

    public function getPhpExecutable(): string
    {
        $executable = (new PhpExecutableFinder())->find();

        if (!$executable) {
            throw new \RuntimeException('Could not find PHP executable.');
        }

        return $executable;
    }

    public function getBootstrapFiles(): array
    {
        return $this->bootstrapFiles;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function isLinterDisabled(): bool
    {
        return $this->disableLint;
    }

    public function disableLint()
    {
        $this->disableLint = true;
    }

    public function enableLint()
    {
        $this->disableLint = false;
    }

    public function disableExecution()
    {
        $this->disableExecute = true;
    }

    public function enableExecution()
    {
        $this->disableExecute = false;
    }

    public function isExecutionDisabled(): bool
    {
        return $this->disableExecute;
    }

    public function shouldStopOnError(): bool
    {
        return $this->stopOnError;
    }

    public function stopOnError()
    {
        $this->stopOnError = true;
    }

    public function continueOnError()
    {
        $this->stopOnError = false;
    }

    public function getFinder(): PHPFilesFinder
    {
        return PHPFilesFinder::create()->in($this->target);
    }
}
