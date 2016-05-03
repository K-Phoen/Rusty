<?php

function rusty_assert($originalCode, $value, $msg = null)
{
    if (!$value) {
        fprintf(STDERR, '[Rusty] Assertion failed: "%s"', $originalCode);
        exit(1);
    }
}
