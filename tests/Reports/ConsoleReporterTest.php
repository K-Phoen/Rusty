<?php

namespace Rusty\Tests\Reports;

use PHPUnit\Framework\TestCase;
use Rusty\CodeSample;
use Rusty\Executor\Result;
use Rusty\Lint\Exception\SyntaxError;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Rusty\Reports;

class ConsoleReporterTest extends TestCase
{
    /**
     * @dataProvider reportProvider
     */
    public function testHandledReportsAreDisplayed(Reports\Report $report): void
    {
        $output = $this->createMock(OutputInterface::class);
        $formatter = $this->createMock(FormatterHelper::class);
        $reporter = new Reports\ConsoleReporter($output, $formatter);

        $output
            ->expects($this->atLeast(1))
            ->method('writeln');

        $reporter->report($report);
    }

    public function reportProvider()
    {
        $splFile = $this->getMockBuilder(\SplFileInfo::class)->disableOriginalConstructor()->getMock();
        $codeSample = $this->getMockBuilder(CodeSample::class)->disableOriginalConstructor()->getMock();
        $result = $this->getMockBuilder(Result::class)->disableOriginalConstructor()->getMock();

        return [
            [new Reports\AnalyseFile($splFile)],
            [new Reports\CodeSampleFound($codeSample)],
            [new Reports\CodeSampleLinted($codeSample)],
            [new Reports\CodeSampleLintFailure($codeSample, new SyntaxError())],
            [new Reports\CodeSampleSkipped($codeSample)],
            [new Reports\ExecutionFailedAsExpected($codeSample, $result)],
            [new Reports\ExecutionFailure($codeSample, $result, new \Exception())],
            [new Reports\ExecutionShouldHaveFailed($codeSample, $result)],
            [new Reports\SuccessfulExecution($codeSample, $result)],
        ];
    }
}
