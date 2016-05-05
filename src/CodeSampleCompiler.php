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
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new AsserterTransformerVisitor());

        $nodes = $this->parser->parse('<?php ' . $sample->getCode());
        $rewrittenNodes = $traverser->traverse($nodes);
        $preparedCode = (new PrettyPrinter\Standard())->prettyPrint($rewrittenNodes);

        $prependCode = '';

        foreach ($context->getBootstrapFiles() as $file) {
            $prependCode .= sprintf('require_once "%s";', $file) . PHP_EOL;
        }

        $prependCode .= sprintf('require_once "%s";', $sample->getFile()->getRealPath()) . PHP_EOL;

        return '<?php' . PHP_EOL . $prependCode . $preparedCode;
    }
}
