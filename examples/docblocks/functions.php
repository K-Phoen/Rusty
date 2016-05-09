<?php

/**
 * Computes the n-th Fibonacci's number.
 *
 * Examples:
 *
 * ```
 * assert(fibonacci(1) === 1);
 * assert(fibonacci(2) === 1);
 * assert(fibonacci(12) === 144);
 * ```
 *
 * ```should_throw
 * // -1 is invalid, some kind of error is expected
 * fibonacci(-1);
 * ```
 *
 * ```no_run
 * // it would take too much time to compute, we don't want to wait that long.
 * fibonacci(10000);
 * ```
 */
function fibonacci($n)
{
    if ($n < 0) {
        throw new \DomainException();
    }

    return $n <= 2 ? 1 : fibonacci($n - 1) + fibonacci($n - 2);
}
