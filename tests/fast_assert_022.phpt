--TEST--
Assert->are_not_same() function - basic test to ensure that are_not_same works as expected
--FILE--
<?php
require_once "test_helper.php";
will_throw(function () { Assert::argument()->are_not_same(123456, 123456); });
will_throw(function () { Assert::argument()->are_not_same(0.0, 0.0); });
will_throw(function () { Assert::argument()->are_not_same("3.14159", "3.14159"); });
will_throw(function () { Assert::argument()->are_not_same(2, 2)->are_not_same('123', '123'); });
will_throw(function () { Assert::argument()->are_not_same(false, false); });
will_throw(function () { Assert::argument()->are_not_same(['hello' => 123], ['hello' => 123]); });
$a = new stdClass();
will_throw(function () use ($a) { Assert::argument()->are_not_same($a, $a); });
will_throw(function () { Assert::argument()->are_not_same(null, null); });

will_not_throw(function () use ($a) { Assert::argument()->are_not_same(new stdClass(), $a); });
will_not_throw(function () { Assert::argument()->are_not_same(['hello' => 'world'], ['hello' => 123]); });
will_not_throw(function () { Assert::argument()->are_not_same(['hello'], 'hello'); });
will_not_throw(function () { Assert::argument()->are_not_same(123456, '123456'); });
will_not_throw(function () { Assert::argument()->are_not_same(0.0, 0.1); });
will_not_throw(function () { Assert::argument()->are_not_same(3.14159, "3.14159"); });
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
No exception thrown
No exception thrown
No exception thrown
No exception thrown
No exception thrown
No exception thrown
