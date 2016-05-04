<?php

declare(strict_types=1);

namespace Rusty\Lint;

use PhpParser\Error as ParserError;
use PhpParser\ParserFactory;

use Rusty\CodeSample;
use Rusty\ExecutionContext;

class PHPParserLinter implements Linter
{
    private $parser;

    public function __construct($kind = ParserFactory::PREFER_PHP7)
    {
        $this->parser = (new ParserFactory)->create($kind);
    }

    public function lint(CodeSample $sample, ExecutionContext $context)
    {
        try {
            $this->parser->parse('<?php ' . $sample->getCode());
        } catch (ParserError $e) {
            throw Exception\SyntaxError::inCodeSample($sample, $e->getMessage());
        }
    }
}
