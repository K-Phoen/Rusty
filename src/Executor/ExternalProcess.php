<?php

namespace Rusty\Executor;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use Symfony\Component\Process\Process;

use Rusty\CodeSample;
use Rusty\ExecutionContext;
use Rusty\PhpParser\AsserterTransformerVisitor;

/**
 * TODO refactor
 */
class ExternalProcess implements Executor
{
    private $parser;

    public function __construct()
    {
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
    }

    public function execute(CodeSample $sample, ExecutionContext $context)
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new AsserterTransformerVisitor());

        $nodes = $this->parser->parse('<?php ' . $sample->getCode());
        $rewrittenNodes = $traverser->traverse($nodes);
        $preparedCode = (new PrettyPrinter\Standard())->prettyPrint($rewrittenNodes);

        $prependCode = '';

        foreach ($context->getBootstrapFiles() as $file) {
            $prependCode .= sprintf('require_once "%s";', $file) . PHP_EOL;
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'rusty_');
        file_put_contents($tmpFile, '<?php ' . PHP_EOL . $prependCode . $preparedCode);

        $process = new Process(sprintf('%s %s', $context->getPhpExecutable(), $tmpFile));
        $process->run();

        unlink($tmpFile);

        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Got error: '.$process->getErrorOutput());
        }
    }
}
