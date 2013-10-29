--TEST--
Assert->is_boolean() function - basic test to ensure that is_boolean works as expected
--FILE--
<?php
require_once "test_helper.php";
will_not_throw(function () { Assert::argument()->is_boolean(true); });
will_not_throw(function () { Assert::argument()->is_boolean(false); });
will_not_throw(function () { Assert::argument()->is_boolean(true)->is_boolean(false); });

will_throw(function () { Assert::argument()->is_boolean(42); });
will_throw(function () { Assert::argument()->is_boolean(null); });
will_throw(function () { Assert::argument()->is_boolean(new stdClass()); });
will_throw(function () { Assert::argument()->is_boolean(0.0); });
will_throw(function () { Assert::argument()->is_boolean(['hello' => 'world']); });
will_throw(function () { Assert::argument()->is_boolean(['hello']); });
will_throw(function () { Assert::argument()->is_boolean('o'); });
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
