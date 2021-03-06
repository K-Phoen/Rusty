<?php

declare(strict_types=1);

namespace Rusty\Reports;

use Rusty\CodeSample;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleReporter implements Reporter
{
    private $output;

    private $formatter;

    public function __construct(OutputInterface $output, FormatterHelper $formatter)
    {
        $this->output = $output;
        $this->formatter = $formatter;
    }

    public function report(Report $report)
    {
        if ($report instanceof AnalyseFile) {
            $this->output->writeln(sprintf('<comment>⚑</comment> Analysing file <comment>%s</comment>', $report->getFile()->getPathname()), OutputInterface::VERBOSITY_VERBOSE);
        } elseif ($report instanceof CodeSampleFound) {
            $this->output->writeln(sprintf(' → Found code sample in line %d', $report->getSample()->getLine()), OutputInterface::VERBOSITY_VERBOSE);
            $this->output->writeln($this->formatSample($report->getSample()), OutputInterface::VERBOSITY_VERY_VERBOSE);
        } elseif ($report instanceof CodeSampleSkipped) {
            $this->output->writeln(' <comment>⚐</comment> Skipped', OutputInterface::VERBOSITY_VERBOSE);
        } elseif ($report instanceof CodeSampleLinted) {
            $this->output->writeln(' <info>✔</info> No syntax error detected', OutputInterface::VERBOSITY_VERBOSE);
        } elseif ($report instanceof CodeSampleLintFailure) {
            $sample = $report->getSample();

            $this->output->writeln(sprintf(' <error>✘</error> Syntax error in %s:%s', $sample->getFile()->getPathname(), $sample->getLine()), OutputInterface::VERBOSITY_NORMAL);
            $this->output->writeln(sprintf('%s', $this->formatSample($sample)), OutputInterface::VERBOSITY_NORMAL);

            $message = explode("\n", trim(sprintf("[%s]\n%s", get_class($report->getError()), $report->getError()->getMessage())));
            $this->output->writeln($this->formatter->formatBlock($message, 'error', true), OutputInterface::VERBOSITY_NORMAL);
        } elseif ($report instanceof SuccessfulExecution) {
            $this->output->writeln(' <info>✔</info> Execution OK', OutputInterface::VERBOSITY_VERBOSE);
        } elseif ($report instanceof ExecutionFailedAsExpected) {
            $this->output->writeln(' <info>✔</info> Execution failed as expected', OutputInterface::VERBOSITY_VERBOSE);
            $this->output->writeln($report->getResult()->getErrorOutput(), OutputInterface::VERBOSITY_VERY_VERBOSE);
        } elseif ($report instanceof ExecutionShouldHaveFailed) {
            $this->output->writeln(' <error>✘</error> Execution was expected to fail but did not', OutputInterface::VERBOSITY_VERBOSE);
            $this->output->writeln($report->getResult()->getOutput(), OutputInterface::VERBOSITY_VERY_VERBOSE);
        } elseif ($report instanceof ExecutionFailure) {
            $sample = $report->getSample();

            $this->output->writeln(sprintf(' <error>✘</error> Got an error while executing code sample found in %s:%s:', $sample->getFile()->getPathname(), $sample->getLine()), OutputInterface::VERBOSITY_NORMAL);
            $this->output->writeln(sprintf('%s', $this->formatSample($sample)), OutputInterface::VERBOSITY_NORMAL);

            $message = explode("\n", trim(sprintf("[%s]\n%s", get_class($report->getException()), $report->getException()->getMessage())));
            $this->output->writeln($this->formatter->formatBlock($message, 'error', true), OutputInterface::VERBOSITY_NORMAL);
        }
    }

    private function formatSample(CodeSample $sample): string
    {
        return sprintf("```%s\n%s\n```", implode(' ', $sample->getPragmaDirectives()), $sample->getCode());
    }
}
