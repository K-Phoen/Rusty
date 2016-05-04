<?php

namespace Rusty\Runtime;

class Asserter
{
    public static function assert($originalCode, $value, $msg = null)
    {
        if (!$value) {
            fprintf(STDERR, '[Rusty] Assertion failed: "%s"', $originalCode);

            if ($msg) {
                printf(STDERR, ' ' . $msg);
            }

            exit(1);
        }
    }
}