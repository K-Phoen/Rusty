<?php

declare(strict_types=1);

namespace Rusty\Console\Command;

use Rusty\Reports\ConsoleReporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Rusty\ExecutionContext;
use Rusty\Extractor;
use Rusty\Rusty;

class Check extends Command
{
    protected function configure()
    {
        $this
            ->setName('check')
            ->setDefinition([
                new InputArgument('target', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'The targets to check.'),
                new InputOption('no-lint', '', InputOption::VALUE_NONE, 'Do not lint any of the code samples.'),
                new InputOption('no-execute', '', InputOption::VALUE_NONE, 'Do not execute any of the code samples.'),
                new InputOption('stop-on-error', '', InputOption::VALUE_NONE, 'Stop the execution if an error happens.'),
                new InputOption('bootstrap-file', '', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'File to include during the execution of a code sample.', []),
                new InputOption('allow-extension', '', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'File extensions to include during the analysis.', $this->defaultAllowedExtensions()),
            ])
            ->setDescription('Check a directory or a file.')
        ;
    }

    private function defaultAllowedExtensions()
    {
        return array_merge(
            Extractor\Markdown::supportedExtensions(),
            Extractor\PhpDoc::supportedExtensions()
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targets = $input->getArgument('target');
        $bootstrapFiles = $input->getOption('bootstrap-file');
        $allowedExtension = $input->getOption('allow-extension');
        $executionContext = new ExecutionContext($targets, $bootstrapFiles, $allowedExtension);

        if ($input->getOption('no-lint')) {
            $executionContext->disableLint();
        }

        if ($input->getOption('no-execute')) {
            $executionContext->disableExecution();
        }

        if ($input->getOption('stop-on-error')) {
            $executionContext->stopOnError();
        }

        $reporter = new ConsoleReporter($output, $this->getHelper('formatter'));

        $success = (new Rusty($reporter))->check($executionContext);

        return $success ? 0 : 1;
    }
}
