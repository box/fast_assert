--TEST--
Assert->is_float() function - basic test to ensure that is_float works as expected
--FILE--
<?php
require_once "test_helper.php";
will_not_throw(function () { Assert::argument()->is_float(0.0); });
will_not_throw(function () { Assert::argument()->is_float(-1.23); });
will_not_throw(function () { Assert::argument()->is_float(2.1)->is_float(0.0); });

will_throw(function () { Assert::argument()->is_float(false); });
will_throw(function () { Assert::argument()->is_float(null); });
will_throw(function () { Assert::argument()->is_float(new stdClass()); });
will_throw(function () { Assert::argument()->is_float(0); });
will_throw(function () { Assert::argument()->is_float(['hello' => 'world']); });
will_throw(function () { Assert::argument()->is_float(['hello']); });
will_throw(function () { Assert::argument()->is_float('o'); });
?>
--EXPECT--
No exception thrown
No exception thrown
No exception thrown
Exception thrown
Exception thrown
Exception thrown
Exception thrown
Exception thrown
Exception thrown
Exception thrown
