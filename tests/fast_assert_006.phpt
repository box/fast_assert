--TEST--
Assert instance method error messages - ensure each method throws proper string
--FILE--
<?php
try { Assert::argument()->is_true(false); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_truthy(false); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_false(true); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_integer(true); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_float(true); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_array(true); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_object(true); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_string(true); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_boolean(42); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_not_null(null); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_key_in_array('asdf', ['asdf']); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_key_in_array('asdf', 'not-an-array'); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_numeric('abc'); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_not_numeric('123'); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_integery('123.234'); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_scalar(null); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->are_same(123, "123"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->are_not_same(123, 123); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->are_equal(123, "1234"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->are_not_equal(123, '123'); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_callable(123); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_instance_of(new stdClass(), 123); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_classname('fakeclass'); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->uses_trait(new stdClass(), 123); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_associative_array([1,2,3]); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_in_array([1,2,3], 4); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_empty([1,2,3]); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_not_empty(null); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }
?>
--EXPECT--
The statement (bool) false was not true
The statement (bool) false was not truthy
The statement (bool) true was not false
The value (bool) true is not an integer
The value (bool) true is not a float
The value (bool) true is not an array
The value (bool) true is not an object
The value (bool) true is not a string
The value (int) 42 is not a boolean
The given value should not have been null
The key (string) asdf was not in Array
Argument 2 passed to is_key_in_array is (string) not-an-array, not an array
The value (string) abc is not numeric
The value (string) 123 is numeric
The value (string) 123.234 does not represent an integer
The value null is not a scalar
The values (int) 123 and (string) 123 are not identical
The values (int) 123 and (int) 123 are identical
The values (int) 123 and (string) 1234 are not equal
The values (int) 123 and (string) 123 are equal
The value (int) 123 is not callable
The value Object is not an instance of (int) 123
The value (string) fakeclass is not a class name
The value Object does not use the trait (int) 123
The value Array is not an associative array
The value (int) 4 is not in the array Array
The value Array is not empty
The value null is empty
