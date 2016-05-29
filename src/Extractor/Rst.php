<?php

declare(strict_types=1);

namespace Rusty\Extractor;

use Gregwar\RST\ErrorManager;
use Gregwar\RST\HTML\Nodes\CodeNode;
use Gregwar\RST\Parser;
use League\CommonMark\Block\Element;
use Rusty\CodeSample;
use Rusty\PragmaParser;

class Rst implements SampleExtractor
{
    protected $pragmaParser;

    public function __construct()
    {
        $this->pragmaParser = new PragmaParser();
    }

    public static function supportedExtensions(): array
    {
        return ['rst'];
    }

    public function extractSamples(\SplFileInfo $file): \Traversable
    {
        $parser = new Parser();

        $parser->getEnvironment()->setErrorManager(new class() extends ErrorManager {
            public function error($message)
            {

            }
        });

        $fileContent = file_get_contents($file->getRealPath());

        /** @var \Gregwar\RST\HTML\Document $document */
        $document = @$parser->parse($fileContent);
        $phpCodeNodes = $document->getNodes(function($node) {
            return $node instanceof CodeNode && $node->getLanguage() === 'php';
        });

        foreach ($phpCodeNodes as $node) {
            $content = $node->getValue();
            $pragmaDirectives = $this->pragmaParser->getPragmaDirectives($content);
            yield new CodeSample($file, 0, $content, $pragmaDirectives);
        }
    }
}
