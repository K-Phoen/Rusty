<?php

/**
 * Returns 42.
 *
 * Examples:
 *
 * ```
 * assert(has_docblock_and_a_single_sample() === 42);
 * ```
 */
function has_docblock_and_a_single_sample()
{
    return 42;
}

/**
 * Returns 42.
 *
 * Examples:
 *
 * ```
 * assert(has_docblock_and_samples() === 42);
 * ```
 *
 * ```no_run
 * assert(has_docblock_and_samples() === 44);
 * ```
 *
 * ```ignore
 * assert(has_docblock_and_samples() === 42 && && fatal())
 * ```
 */
function has_docblock_and_samples()
{
    return 42;
}

function has_no_docblock()
{
    return 42;
}

/**
 * @return int
 */
function has_docblock_but_no_sample()
{
    return 42;
}

/**
 * ```ignore
 * has_syntax_error_in_sample(...)
 * ```
 */
function has_syntax_error_in_sample()
{
    return 42;
}

/**
 * ```should_throw
 * throw new \RuntimeException('Expected exception!');
 * ```
 */
function throws_an_exception_during_execution()
{
    return 42;
}

/**
 * ```ignore should_throw
 * echo "foo !";
 * ```
 */
function says_it_will_throw_but_does_not()
{
    return 42;
}