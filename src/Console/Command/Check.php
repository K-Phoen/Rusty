<?php

namespace Rusty\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Rusty\ExecutionContext;
use Rusty\Rusty;

class Check extends Command
{
    protected function configure()
    {
        $this
            ->setName('check')
            ->setDefinition([
                new InputArgument('target', InputArgument::REQUIRED, 'The target to check.'),
                new InputOption('no-lint', '', InputOption::VALUE_NONE, 'Do not lint any of the code samples.'),
                new InputOption('no-execute', '', InputOption::VALUE_NONE, 'Do not execute any of the code samples.'),
            ])
            ->setDescription('Check a directory or a file.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $target = $input->getArgument('target');
        $executionContext = new ExecutionContext($target);

        if ($input->getOption('no-lint')) {
            $executionContext->disableLint();
        }

        if ($input->getOption('no-execute')) {
            $executionContext->disableExecution();
        }

        (new Rusty())->check($executionContext);
    }
}
