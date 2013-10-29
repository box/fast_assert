--TEST--
Assert->is_integer() function - basic test to ensure that is_integer works as expected
--FILE--
<?php
require_once "test_helper.php";
will_not_throw(function () { Assert::argument()->is_integer(0); });
will_not_throw(function () { Assert::argument()->is_integer(1); });
will_not_throw(function () { Assert::argument()->is_integer(2)->is_integer(0); });

will_throw(function () { Assert::argument()->is_integer(false); });
will_throw(function () { Assert::argument()->is_integer(null); });
will_throw(function () { Assert::argument()->is_integer(new stdClass()); });
will_throw(function () { Assert::argument()->is_integer(0.0); });
will_throw(function () { Assert::argument()->is_integer(['hello' => 'world']); });
will_throw(function () { Assert::argument()->is_integer(['hello']); });
will_throw(function () { Assert::argument()->is_integer('o'); });
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
