<?php

declare(strict_types=1);

namespace Rusty\Reports;

use Rusty\CodeSample;
use Rusty\Executor\Result;

abstract class ExecutionReport extends CodeSampleReport
{
    private $result;

    public function __construct(CodeSample $sample, Result $result)
    {
        parent::__construct($sample);

        $this->result = $result;
    }

    public function getResult(): Result
    {
        return $this->result;
    }
}
