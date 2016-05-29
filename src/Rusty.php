<?php

declare(strict_types=1);

namespace Rusty;

use Rusty\Executor;
use Rusty\Extractor;
use Rusty\Lint;
use Rusty\Reports;

class Rusty
{
    const VERSION = '@package_version@';
    const RELEASE_DATE = '@release_date@';

    /** @var array<string,\Rusty\Extractor\SampleExtractor> */
    private $sampleExtractors;

    /** @var \Rusty\Lint\Linter */
    private $linter;

    /** @var \Rusty\Executor\Executor */
    private $executor;

    /** @var Reports\Reporter */
    private $reporter;

    public function __construct(Reports\Reporter $reporter = null)
    {
        $this->reporter = $reporter ?: new Reports\BlackholeReporter();

        $this->linter = new Lint\PHPParserLinter();
        $this->executor = new Executor\ExternalProcess();

        $this->registerExtractor(new Extractor\PhpDoc());
        $this->registerExtractor(new Extractor\Markdown());
        $this->registerExtractor(new Extractor\Rst());
    }

    public function registerExtractor(Extractor\SampleExtractor $extractor)
    {
        foreach ($extractor::supportedExtensions() as $extension) {
            $this->sampleExtractors[$extension] = $extractor;
        }
    }

    public function check(ExecutionContext $context): bool
    {
        $success = true;

        foreach ($context->getTargets() as $file) {
            $success = $this->checkFile($file, $context) && $success;
        }

        return $success;
    }

    private function checkFile(\SplFileInfo $file, ExecutionContext $context): bool
    {
        $success = true;
        $extractor = $this->findExtractor($file);

        if (!$extractor) {
            return true;
        }

        $this->reporter->report(new Reports\AnalyseFile($file));

        /** @var CodeSample $sample */
        foreach ($extractor->extractSamples($file) as $sample) {
            $this->reporter->report(new Reports\CodeSampleFound($sample));

            try {
                $this->checkSample($sample, $context);
            } catch (\Exception $e) {
                $success = false;

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
            $this->lintSample($sample, $context);
        }

        if (!$context->isExecutionDisabled() && !$sample->hasPragma(PragmaParser::NO_RUN)) {
            $this->executeSample($sample, $context);
        }
    }

    private function lintSample(CodeSample $sample, ExecutionContext $context)
    {
        try {
            $this->linter->lint($sample, $context);
            $this->reporter->report(new Reports\CodeSampleLinted($sample));
        } catch (Lint\Exception\SyntaxError $e) {
            $this->reporter->report(new Reports\CodeSampleLintFailure($sample, $e));
            throw $e;
        }
    }

    public function executeSample(CodeSample $sample, ExecutionContext $context)
    {
        $result = $this->executor->execute($sample, $context);

        if ($sample->hasPragma(PragmaParser::SHOULD_THROW) && !$result->isSuccessful()) {
            $this->reporter->report(new Reports\ExecutionFailedAsExpected($sample, $result));
        } else if ($sample->hasPragma(PragmaParser::SHOULD_THROW) && $result->isSuccessful()) {
            $this->reporter->report(new Reports\ExecutionShouldHaveFailed($sample, $result));

            throw Executor\Exception\ExecutionError::inCodeSample($sample, $result->getErrorOutput());
        } else if (!$result->isSuccessful()) {
            $error = Executor\Exception\ExecutionError::inCodeSample($sample, $result->getErrorOutput());
            $this->reporter->report(new Reports\ExecutionFailure($sample, $result, $error));

            throw $error;
        } else {
            $this->reporter->report(new Reports\SuccessfulExecution($sample, $result));
        }
    }

    /**
     * @return Extractor\SampleExtractor
     */
    private function findExtractor(\SplFileInfo $file)
    {
        if (!array_key_exists($file->getExtension(), $this->sampleExtractors)) {
            return null;
        }

        return $this->sampleExtractors[$file->getExtension()];
    }
}
