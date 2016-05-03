<?php

declare(strict_types=1);

namespace Rusty\Extractor;

use PhpParser\Node;
use PhpParser\ParserFactory;

use Rusty\CodeSample;
use Rusty\PhpParser\NodeCollector;

class PHPDocSampleExtractor implements SampleExtractor
{
    private $parser;

    public function __construct()
    {
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->collectors = [
            new NodeCollector(Node\Stmt\Function_::class),
            new NodeCollector(Node\Stmt\Class_::class),
            new NodeCollector(Node\Stmt\ClassMethod::class),
        ];
    }

    public function extractSamples(\SplFileInfo $file): \Traversable
    {
        $nodes = $this->parser->parse(file_get_contents($file->getPathname()));

        foreach ($this->collectors as $collector) {
            foreach ($collector->collect($nodes) as $node) {
                $comment = $node->getDocComment();

                if (!$comment) {
                    continue;
                }

                foreach ($this->extractFromDocBlock($comment->getText()) as $codeSample) {
                    yield new CodeSample($file, $comment->getLine(), $codeSample);
                }
            }
        }
    }

    private function extractFromDocBlock(string $docBlock): \Generator
    {
        $commentContent = $this->stripCommentStructure($docBlock);
        $matches = [];

        if (!preg_match_all("/```(.*)```/simU", $commentContent, $matches, PREG_SET_ORDER)) {
            return;
        }

        foreach ($matches as $match) {
            yield trim($match[1]);
        }
    }

    private function stripCommentStructure(string $docBlock): string
    {
        return trim(implode("\n", array_map(function($line) {
            return ltrim($line, ' */');
        }, explode("\n", $docBlock))));
    }
}
