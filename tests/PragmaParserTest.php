<?php

namespace Rusty\Tests;

use Rusty\PragmaParser;

class PragmaParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider inputProvider
     */
    public function testItParsesPragmaDirectives(string $input, array $expectedDirectives)
    {
        $parser = new PragmaParser();

        $this->assertEquals($expectedDirectives, $parser->getPragmaDirectives($input));
    }

    public function inputProvider()
    {
        return [
            [ '', [] ],
            [ '  ignore ', [PragmaParser::IGNORE] ],

            // known directives
            [ 'ignore', [PragmaParser::IGNORE] ],
            [ 'no_run', [PragmaParser::NO_RUN] ],
            [ 'should_throw', [PragmaParser::SHOULD_THROW] ],

            // unknown directives are silently ignored
            [ 'unknown_directive', [] ],

            // it makes no sense (for the moment), but several directives can be recognized
            [ 'no_run should_throw', [PragmaParser::NO_RUN, PragmaParser::SHOULD_THROW] ],
            [ ' ignore  no_run ', [PragmaParser::IGNORE, PragmaParser::NO_RUN] ],
        ];
    }
}