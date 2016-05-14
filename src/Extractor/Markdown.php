<?php

declare(strict_types=1);

namespace Rusty\Extractor;

use League\CommonMark\Block\Element;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use Rusty\CodeSample;
use Rusty\PragmaParser;

class Markdown implements SampleExtractor
{
    private $pragmaParser;

    public function __construct()
    {
        $this->pragmaParser = new PragmaParser();
    }

    public static function supportedExtensions(): array
    {
        return ['md', 'markdown', 'mkd'];
    }

    public function extractSamples(\SplFileInfo $file): \Traversable
    {
        $parser = new DocParser(Environment::createCommonMarkEnvironment());
        $documentAST = $parser->parse(file_get_contents($file->getRealPath()));
        $walker = $documentAST->walker();

        while ($event = $walker->next()) {
            $node = $event->getNode();

            if (!$event->isEntering()) {
                continue;
            }

            if (!$node instanceof Element\AbstractBlock || !$node->isCode()) {
                continue;
            }

            if ($node instanceof Element\FencedCode) {
                $infoWords = array_map('strtolower', array_filter(array_map('trim', $node->getInfoWords())));

                // filter code blocks that are not explicitly declared as PHP
                if (!$infoWords || !in_array('php', $infoWords, true)) {
                    continue;
                }
            }

            yield $this->buildCodeSample($file, $node);
        }
    }

    private function buildCodeSample(\SplFileInfo $file, Element\AbstractBlock $node): CodeSample
    {
        $content = $node->getStringContent();
        $pragmaDirectives = $this->pragmaParser->getPragmaDirectives($content);

        return new CodeSample($file, $node->getStartLine(), $content, $pragmaDirectives);
    }
}
