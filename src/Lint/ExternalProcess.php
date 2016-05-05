<?php

declare(strict_types=1);

namespace Rusty\Lint;

use Rusty\CodeSample;
use Rusty\ExecutionContext;
use Symfony\Component\Process\Process;

class ExternalProcess implements Linter
{
    public function lint(CodeSample $sample, ExecutionContext $context): bool
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'rusty_');
        file_put_contents($tmpFile, '<?php' . PHP_EOL . $sample->getCode());

        $process = new Process(sprintf('%s -l %s', $context->getPhpExecutable(), $tmpFile));
        $process->run();

        unlink($tmpFile);

        if (!$process->isSuccessful()) {
            throw Exception\SyntaxError::inCodeSample($sample, $process->getErrorOutput());
        }

        return true;
    }
}
