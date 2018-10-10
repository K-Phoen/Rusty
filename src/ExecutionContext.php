<?php

declare(strict_types=1);

namespace Rusty;

use Symfony\Component\Process\PhpExecutableFinder;

use Rusty\Finder\FilesFinder;

/**
 * Stores all the runtime execution context.
 *
 * Examples:
 *
 * ```
 * $context = new ExecutionContext(['./some/file.php', './other/file.php']);
 * $context = new ExecutionContext(['./some/directory/']);
 * ```
 *
 * Bootstrap files can be specified:
 * ```
 * $context = new ExecutionContext(['./some/file.php'], [
 *   './some/bootstrap/file.php',
 * ]);
 * ```
 *
 * The search can be restricted to some extensions:
 * ```
 * $context = new ExecutionContext(['./some/directory/'], [], ['php']);
 * ```
 *
 * Some options define what will be checked:
 * ```
 * $context = new ExecutionContext(['./some/directory/']);
 * $context->disableLint();
 * $context->stopOnError();
 * ```
 */
class ExecutionContext
{
    private $targets;

    private $disableLint = false;

    private $disableExecute = false;

    private $stopOnError = false;

    private $bootstrapFiles = [];

    private $allowedExtensions = [];

    public function __construct(array $targets, array $bootstrapFiles = [], array $allowedExtensions = [])
    {
        $this->targets = $targets;
        $this->bootstrapFiles = $bootstrapFiles;
        $this->allowedExtensions = $allowedExtensions;
    }

    public function getPhpExecutable(): string
    {
        $executable = (new PhpExecutableFinder())->find();

        if (!$executable) {
            throw new \RuntimeException('Could not find PHP executable.');
        }

        return $executable;
    }

    public function getBootstrapFiles(): array
    {
        return $this->bootstrapFiles;
    }

    public function isLinterDisabled(): bool
    {
        return $this->disableLint;
    }

    public function disableLint()
    {
        $this->disableLint = true;
    }

    public function enableLint()
    {
        $this->disableLint = false;
    }

    public function disableExecution()
    {
        $this->disableExecute = true;
    }

    public function enableExecution()
    {
        $this->disableExecute = false;
    }

    public function isExecutionDisabled(): bool
    {
        return $this->disableExecute;
    }

    public function shouldStopOnError(): bool
    {
        return $this->stopOnError;
    }

    public function stopOnError()
    {
        $this->stopOnError = true;
    }

    public function continueOnError()
    {
        $this->stopOnError = false;
    }

    public function getTargets(): \Traversable
    {
        foreach ($this->targets as $target) {
            if (is_file($target)) {
                $fileInfo = new \SplFileInfo($target);

                if ($this->allowedExtensions && !in_array($fileInfo->getExtension(), $this->allowedExtensions, true)) {
                    continue;
                }

                yield from new \ArrayIterator([$fileInfo]);
            } else {
                $finder = FilesFinder::create()->in($target);

                foreach ($this->allowedExtensions as $extension) {
                    $finder->name('*.'.$extension);
                }

                yield from $finder;
            }
        }
    }
}
