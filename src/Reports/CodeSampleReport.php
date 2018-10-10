<?php

declare(strict_types=1);

namespace Rusty\Reports;

use Rusty\CodeSample;

abstract class CodeSampleReport implements Report
{
    private $sample;

    public function __construct(CodeSample $sample)
    {
        $this->sample = $sample;
    }

    public function getSample(): CodeSample
    {
        return $this->sample;
    }
}
