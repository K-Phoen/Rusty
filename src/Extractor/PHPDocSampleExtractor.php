<?php

declare(strict_types=1);

namespace Rusty\Extractor;

use PhpParser\Node;
use PhpParser\ParserFactory;

use Rusty\CodeSample;
use Rusty\PhpParser\NodeCollector;
use Rusty\PragmaParser;

class PHPDocSampleExtractor implements SampleExtractor
{
    /** @var \PhpParser\Parser */
    private $parser;
    private $pragmaParser;

    public function __construct()
    {
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->pragmaParser = new PragmaParser();
    }

    public function extractSamples(\SplFileInfo $file): \Traversable
    {
        $nodes = $this->parser->parse(file_get_contents($file->getPathname()));
        $collectors = [
            new NodeCollector(Node\Stmt\Function_::class),
            new NodeCollector(Node\Stmt\Class_::class),
            new NodeCollector(Node\Stmt\ClassMethod::class),
        ];

        /** @var NodeCollector $collector */
        foreach ($collectors as $collector) {
            /** @var Node $node */
            foreach ($collector->collect($nodes) as $node) {
                $comment = $node->getDocComment();

                if (!$comment) {
                    continue;
                }

                foreach ($this->extractFromDocBlock($comment->getText()) as $data) {
                    $pragmaDirectives = $this->pragmaParser->getPragmaDirectives($data['pragma']);

                    yield new CodeSample($file, $comment->getLine(), $data['code'], $pragmaDirectives);
                }
            }
        }
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
        return trim(implode("\n", array_map(function($line) {
            return ltrim($line, ' */');
        }, explode("\n", $docBlock))));
    }
}
