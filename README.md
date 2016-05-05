Rusty
=====

The primary way of documenting a Rust project is through annotating the source
code. These annotations can be viewed as part of the documentation, but they can
also be compiled and executed. They call that "documentation as tests" and [their
documentation](https://doc.rust-lang.org/book/documentation.html) is a goldmine.

Rusty is an attempt at implementing the same idea in the PHP world.

Usage
-----

```
./bin/rusty check --bootstrap-file ./vendor/autoload.php -vvv .
```

License
-------

This library is under the [MIT](LICENSE) license.
