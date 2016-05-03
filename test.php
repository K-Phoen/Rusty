<?php

/**
 * Returns 42.
 *
 * Example:
 *
 * ```
 * assert(foo() === 42);
 * ```
 *
 * ```
 * assert(foo() != 44);
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
