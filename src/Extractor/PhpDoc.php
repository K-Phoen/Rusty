<?php

declare(strict_types=1);

namespace Rusty\Extractor;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;

use Rusty\CodeSample;
use Rusty\PhpParser\ContextCollectorResolver;
use Rusty\PhpParser\NameResolver;
use Rusty\PhpParser\NodeCollector;
use Rusty\PragmaParser;

class PhpDoc implements SampleExtractor
{
    /** @var \PhpParser\Parser */
    private $parser;

    private $pragmaParser;

    public function __construct()
    {
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->pragmaParser = new PragmaParser();
    }

    public static function supportedExtensions(): array
    {
        return ['php'];
    }

    public function extractSamples(\SplFileInfo $file): \Traversable
    {
        $ast = $this->getAst(file_get_contents($file->getPathname()));
        list($namespace, $aliases) = $this->collectNamespaceContext($ast);

        $collectors = [
            new NodeCollector(Node\Stmt\Function_::class),
            new NodeCollector(Node\Stmt\Class_::class),
            new NodeCollector(Node\Stmt\ClassMethod::class),
        ];

        /** @var NodeCollector $collector */
        foreach ($collectors as $collector) {
            /** @var Node $node */
            foreach ($collector->collect($ast) as $node) {
                $comment = $node->getDocComment();

                if (!$comment) {
                    continue;
                }

                foreach ($this->extractFromDocBlock($comment->getText()) as $data) {
                    $astSample = $this->getAst('<?php'.PHP_EOL.$data['code']);
                    $pragmaDirectives = $this->pragmaParser->getPragmaDirectives($data['pragma']);

                    $rewrittenAst = $this->resolveNames($astSample, $namespace, $aliases);
                    $rewrittenCode = (new PrettyPrinter\Standard())->prettyPrint($rewrittenAst);

                    yield new CodeSample($file, $comment->getLine(), $rewrittenCode, $pragmaDirectives);
                }
            }
        }
    }

    private function getAst(string $code)
    {
        return $this->parser->parse($code);
    }

    private function collectNamespaceContext($ast): array
    {
        $contextCollector = new ContextCollectorResolver();

        $traverser = new NodeTraverser();
        $traverser->addVisitor($contextCollector);
        $traverser->traverse($ast);

        return [$contextCollector->getNamespace(), $contextCollector->getAliases()];
    }

    private function resolveNames($ast, $namespace, array $aliases)
    {
        $resolver = new NameResolver();
        $resolver->getNameContext()->startNamespace($namespace);

        foreach ($aliases as $type => $groupedAliases) {
            foreach ($groupedAliases as $aliasName => $fqcn) {
                $resolver->getNameContext()->addAlias($fqcn, $aliasName, $type);
            }
        }

        $traverser = new NodeTraverser();
        $traverser->addVisitor($resolver);

        return $traverser->traverse($ast);
    }

    private function extractFromDocBlock(string $docBlock): \Generator
    {
        $commentContent = $this->stripCommentStructure($docBlock);
        $matches = [];

        if (!preg_match_all('/```([\w ]*)\R(.*)```/smU', $commentContent, $matches, PREG_SET_ORDER)) {
            return;
        }

        foreach ($matches as $match) {
            yield ['pragma' => trim($match[1]), 'code' => trim($match[2])];
        }
    }

    private function stripCommentStructure(string $docBlock): string
    {
        $docBlock = preg_replace('`^\s*/\*\*\s*$`mu', '', $docBlock);
        $docBlock = preg_replace('`^\s*\*(.*)$`mu', '$1', $docBlock);
        $docBlock = preg_replace('`^\s*\*/\s*$`mu', '', $docBlock);

        return $docBlock;
    }
}
