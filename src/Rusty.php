<?php

declare(strict_types=1);

namespace Rusty;

use Rusty\Executor;
use Rusty\Extractor\PHPDocSampleExtractor;
use Rusty\Lint\PHPParserLinter;
use Rusty\Reports;

class Rusty
{
    /** @var \Rusty\Extractor\SampleExtractor */
    private $sampleExtractor;

    /** @var \Rusty\Lint\Linter */
    private $linter;

    /** @var \Rusty\Executor\Executor */
    private $executor;

    /** @var Reports\Reporter */
    private $reporter;

    public function __construct(Reports\Reporter $reporter = null)
    {
        $this->reporter = $reporter ?: new Reports\BlackholeReporter();

        $this->sampleExtractor = new PHPDocSampleExtractor();
        $this->linter = new PHPParserLinter();
        $this->executor = new Executor\ExternalProcess();
    }

    public function check(ExecutionContext $context): bool
    {
        $success = true;

        foreach ($context->getFinder() as $file) {
            $success = $success && $this->checkFile($file, $context);
        }

        return $success;
    }

    private function checkFile(\SplFileInfo $file, ExecutionContext $context): bool
    {
        $success = true;

        $this->reporter->report(new Reports\AnalyseFile($file));

        /** @var CodeSample $sample */
        foreach ($this->sampleExtractor->extractSamples($file) as $sample) {
            $this->reporter->report(new Reports\CodeSampleFound($sample));

            try {
                $this->checkSample($sample, $context);
            } catch (\Exception $e) {
                $success = false;
                $this->reporter->report(new Reports\CodeSampleCheckFailed($sample, $e));

                if ($context->shouldStopOnError()) {
                    throw $e;
                }
            }
        }

        return $success;
    }

    private function checkSample(CodeSample $sample, ExecutionContext $context)
    {
        if ($sample->hasPragma(PragmaParser::IGNORE)) {
            $this->reporter->report(new Reports\CodeSampleSkipped($sample));
            return;
        }

        if (!$context->isLinterDisabled()) {
            $this->reporter->report(new Reports\CodeSampleLinted($sample));
            $this->linter->lint($sample, $context);
        }

        if (!$context->isExecutionDisabled() && !$sample->hasPragma(PragmaParser::NO_RUN)) {
            $this->reporter->report(new Reports\CodeSampleExecuted($sample));
            $this->executor->execute($sample, $context);
        }
    }
}
