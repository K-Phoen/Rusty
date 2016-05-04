<?php

namespace Rusty;

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

    public function getPragmaDirectives(string $code)
    {
        $firstLine = strtok($code, "\n");

        $directives = array_filter(explode(' ', $firstLine), function($directive) {
            return in_array($directive, self::KNOWN_DIRECTIVES, true);
        });

        return $directives;
    }
}
