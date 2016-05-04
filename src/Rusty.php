<?php

declare(strict_types=1);

namespace Rusty;

use Rusty\Executor;
use Rusty\Extractor\PHPDocSampleExtractor;
use Rusty\Lint\PHPParserLinter;

class Rusty
{
    /** @var \Rusty\Extractor\SampleExtractor */
    private $sampleExtractor;

    /** @var \Rusty\Lint\Linter */
    private $linter;

    /** @var \Rusty\Executor\Executor */
    private $executor;

    public function __construct()
    {
        $this->sampleExtractor = new PHPDocSampleExtractor();
        $this->linter = new PHPParserLinter();
        $this->executor = new Executor\ExternalProcess();
    }

    public function check(ExecutionContext $context)
    {
        foreach ($context->getFinder() as $file) {
            $this->checkFile($file, $context);
        }
    }

    private function checkFile(\SplFileInfo $file, ExecutionContext $context)
    {
        var_dump('checking file '. $file->getPathname());
        /** @var CodeSample $sample */
        foreach ($this->sampleExtractor->extractSamples($file) as $sample) {
            var_dump('found code sample --->   '. $sample->getCode());

            if ($sample->hasPragma(PragmaParser::IGNORE)) {
                continue;
            }

            if (!$context->isLinterDisabled()) {
                $this->linter->lint($sample, $context);
            }

            if (!$context->isExecutionDisabled() && !$sample->hasPragma(PragmaParser::NO_RUN)) {
                $this->executor->execute($sample, $context);
            }
        }
    }
}
