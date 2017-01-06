# Key

- `Key(mixed $key)`
- `Key(mixed $key, Rule $rule)`
- `Key(mixed $key, Rule $rule, bool $mandatory)`

Validates an array key.

```php
$dict = [
    'foo' => 'bar'
];

v::key('foo')->validate($dict); // true
```

You can also validate the key value itself:

```php
v::key('foo', v::equals('bar'))->validate($dict); // true
```

Third parameter makes the key presence optional:

```php
v::key('lorem', v::stringType(), false)->validate($dict); // true
```

The name of this validator is automatically set to the key name.

***
See also:

  * [Attribute](Attribute.md)
