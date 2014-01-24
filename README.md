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
Assert::received_value()->is_instance_of($obj, "MyDesiredClass");
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

## Copyright and License
 
Copyright 2014 Box, Inc. All rights reserved.
 
Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at
 
   http://www.apache.org/licenses/LICENSE-2.0
 
Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
