<?php

/**
 * ```
 * $obj = new HasDocBlockAndSample();
 * assert($obj->fortyTwo() === 42);
 * assert($obj->fiftyFive() !== 42);
 * ```
 */
class HasDocBlockAndSample
{
    /**
     * ```
     * $obj = new HasDocBlockAndSample();
     * assert($obj->methodWithSample() === 42);
     * ```
     */
    public function methodWithSample()
    {
        return 42;
    }

    public function fiftyFive()
    {
        return 55;
    }

    public function fortyTwo()
    {
        return 42;
    }
}

class HasNoDocBlock
{
    /**
     * ```
     * $obj = new HasNoDocBlock();
     * assert($obj->methodWithSample() === 42);
     * ```
     */
    public function methodWithSample()
    {
        return 42;
    }

    public function fortyTwo()
    {
        return 42;
    }
}
