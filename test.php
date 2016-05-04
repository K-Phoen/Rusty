<?php

/**
 * Returns 42.
 *
 * Examples:
 *
 * ```
 * assert(foo() === 42);
 * ```
 *
 * ```no_run
 * assert(foo() != 44);
 * ```
 * ```
 *
 * ```ignore
 * assert(foo() === 42 && && fatal())
 * ```
 */
function foo()
{
    return 42;
}

function bar()
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
 * ```
 * $foo = new Foo();
 * assert($foo->bar() === 42);
 * assert($foo->lala() !== 42);
 * ```
 */
class Foo
{
    /**
     * ```
     * $foo = new Foo();
     * assert($foo->bar() === 42);
     * ```
     */
    public function methodWithSample()
    {
        return 42;
    }

    public function lala()
    {
        return 55;
    }
}
