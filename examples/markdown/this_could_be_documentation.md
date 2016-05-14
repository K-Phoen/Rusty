# this could be documentation

but it's not.

guess what? it's just random code samples included in a markdown document.

just like this:

```php
echo 'hello world';
```

This one isn't even valid php (so we'll ignore it):

```php
#ignore

function !() {
}
```

This one is syntactically correct but throws an (expected) exception, so we indicate it to Rusty:

```php
#should_throw

throw new Exception('Told you.');
```

And this one uses the network, so we'll avoid running it.

```php
#no_run

file_get_contents('http://www.does-not-exist.foo');
```