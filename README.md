Fast assert is a php extension that provides a nice way of making Assertions in php.
It provides facilities for making various assertions using clean function-chaining syntax without
having to pay any performance costs. For comparison, a functionally-equivalent version
of Assert implemented in PHP is 150 times slower.

Examples
--------

To assert that a value $a is an integer that is greater than 0:

```
Assert::argument()->is_integer($a)->is_true($a > 0);
```

If either assertion fails, this will throw an InvalidArgumentException.

To assert that an object is of a particular type:

```
Assert::receivedValue()->is_instance_of($obj, "MyDesiredClass");
```

If this assertion fails, this will throw an UnexpectedValueException.

See the documentation at docs/stubs/Assert.php for a full listing of
Assert's methods.

Installation
------------

First, you must build the extension with the following commands:

- `phpize`
- `./configure --enable-fast_assert`
- `make`
- `make test`

Then, you can install it with:
- `sudo make install`
- Add the line `extension=fast_assert.so` to your php configuration file of choice. Your php.ini file should work,
or you can try something like `echo "extension=fast_assert.so" > /etc/php.d/fast_assert.ini`
