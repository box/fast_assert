--TEST--
Assert->is_array() function - basic test to ensure that is_array works as expected
--FILE--
<?php
require_once "test_helper.php";
will_not_throw(function () { Assert::argument()->is_array(['hello' => 'world']); });
will_not_throw(function () { Assert::argument()->is_array(['hello']); });
will_not_throw(function () { Assert::argument()->is_array([])->is_array(['asdf', 'fdsa']); });

will_throw(function () { Assert::argument()->is_array(false); });
will_throw(function () { Assert::argument()->is_array(null); });
will_throw(function () { Assert::argument()->is_array(new stdClass()); });
will_throw(function () { Assert::argument()->is_array(0.0); });
will_throw(function () { Assert::argument()->is_array(123); });
will_throw(function () { Assert::argument()->is_array(true); });
will_throw(function () { Assert::argument()->is_array('o'); });
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
