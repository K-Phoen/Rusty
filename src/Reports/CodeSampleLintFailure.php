<?php

declare(strict_types=1);

namespace Rusty\Reports;

use Rusty\CodeSample;
use Rusty\Lint\Exception\SyntaxError;

class CodeSampleLintFailure extends CodeSampleReport
{
    private $error;

    public function __construct(CodeSample $sample, SyntaxError $error)
    {
        parent::__construct($sample);

        $this->error = $error;
    }

    public function getError(): SyntaxError
    {
        return $this->error;
    }
}