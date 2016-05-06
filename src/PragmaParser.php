<?php

namespace Rusty;

/**
 * Parses "pragma" directives found in code samples.
 *
 * Examples:
 *
 * ```
 * $parser = new PragmaParser();
 * $parser->getPragmaDirectives('# ignore');
 * $parser->getPragmaDirectives('ignore');
 * $parser->getPragmaDirectives('no_run should_throw'); // does not make sense but works
 * $parser->getPragmaDirectives('unknown_directive');
 * ```
 */
class PragmaParser
{
    const IGNORE = 'ignore';
    const NO_RUN = 'no_run';
    const SHOULD_THROW = 'should_throw';

    const KNOWN_DIRECTIVES = [
        self::IGNORE,
        self::NO_RUN,
        self::SHOULD_THROW,
    ];

    public function getPragmaDirectives(string $code): array
    {
        $firstLine = strtok($code, "\n");
        $directives = array_filter(array_map('trim', explode(' ', $firstLine)));

        return array_values(array_intersect(self::KNOWN_DIRECTIVES, $directives));
    }
}
