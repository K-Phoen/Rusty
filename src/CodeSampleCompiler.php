<?php

declare(strict_types=1);

namespace Rusty;

use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;

use Rusty\PhpParser\AsserterTransformerVisitor;

class CodeSampleCompiler
{
    /** @var \PhpParser\Parser */
    private $parser;

    public function __construct()
    {
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
    }

    public function compile(CodeSample $sample, ExecutionContext $context): string
    {
        $compiledCode = '<?php';

        // require our bootstrap file
        $compiledCode .= PHP_EOL . sprintf('require_once "%s";', __DIR__ . '/Runtime/bootstrap.php');

        // require user's bootstrap files
        foreach ($context->getBootstrapFiles() as $file) {
            $compiledCode .= PHP_EOL . sprintf('require_once "%s";', $file);
        }

        // require the sample file itself
        $compiledCode .= PHP_EOL . sprintf('require_once "%s";', $sample->getFile()->getRealPath());

        // compile the sample
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new AsserterTransformerVisitor());

        $nodes = $this->parser->parse('<?php ' . $sample->getCode());
        $rewrittenNodes = $traverser->traverse($nodes);
        $compiledCode .= PHP_EOL . (new PrettyPrinter\Standard())->prettyPrint($rewrittenNodes);

        return $compiledCode;
    }
}
