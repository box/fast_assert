--TEST--
Assert->are_not_equal() function - basic test to ensure that are_not_equal works as expected
--FILE--
<?php
require_once "test_helper.php";
will_throw(function () { Assert::argument()->are_not_equal(123456, '123456'); });
will_throw(function () { Assert::argument()->are_not_equal(0.0, "0.0"); });
will_throw(function () { Assert::argument()->are_not_equal("3.14159", "3.14159"); });
will_throw(function () { Assert::argument()->are_not_equal(2, '2')->are_not_equal('123', '123'); });
will_throw(function () { Assert::argument()->are_not_equal(false, false); });
will_throw(function () { Assert::argument()->are_not_equal(['hello' => 123], ['hello' => 123]); });
$a = new stdClass();
will_throw(function () use ($a) { Assert::argument()->are_not_equal($a, $a); });
will_throw(function () { Assert::argument()->are_not_equal(null, null); });
will_throw(function () use ($a) { Assert::argument()->are_not_equal(new stdClass(), $a); });

will_not_throw(function () { Assert::argument()->are_not_equal(['hello' => 'world'], ['hello' => 123]); });
will_not_throw(function () { Assert::argument()->are_not_equal(['hello'], 'hello'); });
will_not_throw(function () { Assert::argument()->are_not_equal(123456, 1234567); });
will_not_throw(function () { Assert::argument()->are_not_equal(0.0, 0.1); });
will_not_throw(function () { Assert::argument()->are_not_equal(3.14159, "3.1124"); });
?>
--EXPECT--
Exception thrown
Exception thrown
Exception thrown
Exception thrown
Exception thrown
Exception thrown
Exception thrown
Exception thrown
Exception thrown
No exception thrown
No exception thrown
No exception thrown
No exception thrown
No exception thrown
