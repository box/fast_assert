--TEST--
Assert->is_true() function - basic test to ensure that is_true works as expected
--FILE--
<?php
require_once "test_helper.php";
will_not_throw(function () { Assert::argument()->is_true(true); });
will_not_throw(function () { Assert::argument()->is_true(1 == 1); });
will_not_throw(function () { Assert::argument()->is_true(1 == 1)->is_true(true); });
will_throw(function () { Assert::argument()->is_true(false); });
will_throw(function () { Assert::argument()->is_true(false)->is_true(true); });
will_throw(function () { Assert::argument()->is_true(1); });
will_throw(function () { Assert::argument()->is_true(new stdClass()); });
will_throw(function () { Assert::argument()->is_true(3.14159); });
will_throw(function () { Assert::argument()->is_true(['hello' => 'world']); });
will_throw(function () { Assert::argument()->is_true(['hello', 'world']); });
will_throw(function () { Assert::argument()->is_true(null); });
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
Exception thrown
