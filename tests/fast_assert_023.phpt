--TEST--
Assert->are_equal() function - basic test to ensure that are_equal works as expected
--FILE--
<?php
require_once "test_helper.php";
will_not_throw(function () { Assert::argument()->are_equal(123456, '123456'); });
will_not_throw(function () { Assert::argument()->are_equal(0.0, "0.0"); });
will_not_throw(function () { Assert::argument()->are_equal("3.14159", "3.14159"); });
will_not_throw(function () { Assert::argument()->are_equal(2, '2')->are_equal('123', '123'); });
will_not_throw(function () { Assert::argument()->are_equal(false, false); });
will_not_throw(function () { Assert::argument()->are_equal(['hello' => 123], ['hello' => 123]); });
$a = new stdClass();
will_not_throw(function () use ($a) { Assert::argument()->are_equal($a, $a); });
will_not_throw(function () { Assert::argument()->are_equal(null, null); });
will_not_throw(function () use ($a) { Assert::argument()->are_equal(new stdClass(), $a); });

will_throw(function () { Assert::argument()->are_equal(['hello' => 'world'], ['hello' => 123]); });
will_throw(function () { Assert::argument()->are_equal(['hello'], 'hello'); });
will_throw(function () { Assert::argument()->are_equal(123456, 1234567); });
will_throw(function () { Assert::argument()->are_equal(0.0, 0.1); });
will_throw(function () { Assert::argument()->are_equal(3.14159, "3.1124"); });
?>
--EXPECT--
No exception thrown
No exception thrown
No exception thrown
No exception thrown
No exception thrown
No exception thrown
No exception thrown
No exception thrown
No exception thrown
Exception thrown
Exception thrown
Exception thrown
Exception thrown
Exception thrown
