Rusty [![Build Status](https://travis-ci.org/K-Phoen/Rusty.svg?branch=master)](https://travis-ci.org/K-Phoen/Rusty) ![PHP7 ready](https://img.shields.io/badge/PHP7-ready-green.svg)
=====

The primary way of documenting a Rust project is through annotating the source
code. These annotations can be viewed as part of the documentation, but they can
also be compiled and executed. They call that "**documentation as tests**" and [their
documentation](https://doc.rust-lang.org/book/documentation.html) is a goldmine.

Rusty is an attempt at implementing the same idea in the **PHP** world.

Usage
-----

Rusty is able to **extract code samples from** both **PHP doc-blocks** and
**Markdown files** (your documentation for instance).

### Running the tests

An executable is provided to analyse the code samples scattered in your documentation and in your doc-blocks:

```
rusty check -v ./src/
```

### Writing documentation as tests

A code sample usually looks like this:

```php
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
 * ```no_execute
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
```

More examples can be found in the [`./examples`](https://github.com/K-Phoen/Rusty/tree/master/examples/)
directory.

Use `rusty help check` for a list of all the possible options.

Installation
------------

Rusty can either be installed globally:

```
composer global require kphoen/rusty dev-master
```

Or locally:

```
composer require kphoen/rusty dev-master
```

Both methods will provide the `rusty` executable.

License
-------

This library is under the [MIT](LICENSE) license.
