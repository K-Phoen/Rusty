<?php

declare(strict_types=1);

namespace Rusty\Extractor;

use League\CommonMark\Block\Element\AbstractBlock;
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

            if (!$node instanceof AbstractBlock || !$node->isCode()) {
                continue;
            }

            /** @var AbstractBlock $node */

            $content = $node->getStringContent();
            $pragmaDirectives = $this->pragmaParser->getPragmaDirectives($content);

            yield new CodeSample($file, $node->getStartLine(), $content, $pragmaDirectives);
        }
    }
}
