--TEST--
Assert->are_same() function - basic test to ensure that are_same works as expected
--FILE--
<?php
require_once "test_helper.php";
will_not_throw(function () { Assert::argument()->are_same(123456, 123456); });
will_not_throw(function () { Assert::argument()->are_same(0.0, 0.0); });
will_not_throw(function () { Assert::argument()->are_same("3.14159", "3.14159"); });
will_not_throw(function () { Assert::argument()->are_same(2, 2)->are_same('123', '123'); });
will_not_throw(function () { Assert::argument()->are_same(false, false); });
will_not_throw(function () { Assert::argument()->are_same(['hello' => 123], ['hello' => 123]); });
$a = new stdClass();
will_not_throw(function () use ($a) { Assert::argument()->are_same($a, $a); });
will_not_throw(function () { Assert::argument()->are_same(null, null); });

will_throw(function () use ($a) { Assert::argument()->are_same(new stdClass(), $a); });
will_throw(function () { Assert::argument()->are_same(['hello' => 'world'], ['hello' => 123]); });
will_throw(function () { Assert::argument()->are_same(['hello'], 'hello'); });
will_throw(function () { Assert::argument()->are_same(123456, '123456'); });
will_throw(function () { Assert::argument()->are_same(0.0, 0.1); });
will_throw(function () { Assert::argument()->are_same(3.14159, "3.14159"); });
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
Exception thrown
Exception thrown
Exception thrown
Exception thrown
Exception thrown
Exception thrown
