<?php


namespace Rusty\Reports;

use Rusty\CodeSample;

class CodeSampleCheckFailed extends CodeSampleReport
{
    private $exception;

    public function __construct(CodeSample $sample, \Exception $exception)
    {
        parent::__construct($sample);

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