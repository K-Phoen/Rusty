<?php

declare(strict_types=1);

namespace Rusty;

use Rusty\Extractor\PHPDocSampleExtractor;
use Rusty\Lint\PHPParserLinter;

class Rusty
{
    /** @var \Rusty\Extractor\SampleExtractor */
    private $sampleExtractor;

    /** @var \Rusty\Lint\Linter */
    private $linter;

    public function __construct()
    {
        $this->sampleExtractor = new PHPDocSampleExtractor();
        $this->linter = new PHPParserLinter();
    }

    public function check(ExecutionContext $context)
    {
        foreach ($context->getFinder() as $file) {
            $this->checkFile($file, $context);
        }
    }

    private function checkFile(\SplFileInfo $file, ExecutionContext $context)
    {
        var_dump('checking file', $file->getPathname());
        /** @var CodeSample $sample */
        foreach ($this->sampleExtractor->extractSamples($file) as $sample) {
            var_dump('found code sample --->   '. $sample->getCode());

            if (!$context->isLinterDisabled()) {
                $this->linter->lint($sample, $context);
            }

            if (!$context->isExecutionDisabled()) {
                $this->executor->execute($sample, $context);
            }
        }
    }
}
