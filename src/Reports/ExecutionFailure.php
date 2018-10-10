<?php

declare(strict_types=1);

namespace Rusty\Reports;

use Rusty\CodeSample;
use Rusty\Executor\Result;

class ExecutionFailure extends ExecutionReport
{
    private $exception;

    public function __construct(CodeSample $sample, Result $result, \Exception $exception)
    {
        parent::__construct($sample, $result);

        $this->exception = $exception;
    }

    /**
     * @return \Exception
     */
    public function getException(): \Exception
    {
        return $this->exception;
    }
}
