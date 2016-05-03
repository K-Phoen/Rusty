<?php

declare(strict_types=1);

namespace Rusty;

use Rusty\Finder\PHPFilesFinder;

class ExecutionContext
{
    private $target;
    private $disableLint = false;
    private $disableExecute = false;

    public function __construct(string $target)
    {
        $this->target = $target;
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

    public function getFinder(): PHPFilesFinder
    {
        return PHPFilesFinder::create()->in($this->target);
    }
}
