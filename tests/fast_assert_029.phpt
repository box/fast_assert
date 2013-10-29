--TEST--, error message horray!@#
Assert error message test - assure that assert properly appends optional error messages to exception messages
--FILE--
<?php
try { Assert::argument()->is_true(false, "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_truthy(false, "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_false(true, "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_integer(true, "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_float(true, "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_array(true, "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_object(true, "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_string(true, "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_boolean(42, "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_not_null(null, "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_key_in_array('asdf', ['asdf'], "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_key_in_array('asdf', 'not-an-array', "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_numeric('abc', "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_not_numeric('123', "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_integery('123.234', "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_scalar(null, "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->are_same(123, "123", "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->are_not_same(123, 123, "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->are_equal(123, "1234", "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->are_not_equal(123, '123', "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_callable(123, "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_instance_of(new stdClass(), 123, "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_classname('fakeclass', "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->uses_trait(new stdClass(), 123, "error message horray!@#"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }
?>
--EXPECT--
The statement (bool) false was not true, error message horray!@#
The statement (bool) false was not truthy, error message horray!@#
The statement (bool) true was not false, error message horray!@#
The value (bool) true is not an integer, error message horray!@#
The value (bool) true is not a float, error message horray!@#
The value (bool) true is not an array, error message horray!@#
The value (bool) true is not an object, error message horray!@#
The value (bool) true is not a string, error message horray!@#
The value (int) 42 is not a boolean, error message horray!@#
The given value should not have been null, error message horray!@#
The key (string) asdf was not in Array, error message horray!@#
Argument 2 passed to is_key_in_array is (string) not-an-array, not an array, error message horray!@#
The value (string) abc is not numeric, error message horray!@#
The value (string) 123 is numeric, error message horray!@#
The value (string) 123.234 does not represent an integer, error message horray!@#
The value null is not a scalar, error message horray!@#
The values (int) 123 and (string) 123 are not identical, error message horray!@#
The values (int) 123 and (int) 123 are identical, error message horray!@#
The values (int) 123 and (string) 1234 are not equal, error message horray!@#
The values (int) 123 and (string) 123 are equal, error message horray!@#
The value (int) 123 is not callable, error message horray!@#
The value Object is not an instance of (int) 123, error message horray!@#
The value (string) fakeclass is not a class name, error message horray!@#
The value Object does not use the trait (int) 123, error message horray!@#

