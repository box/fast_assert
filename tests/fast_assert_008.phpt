--TEST--
Assert->is_false() function - basic test to ensure that is_false works as expected
--FILE--
<?php
require_once "test_helper.php";
will_not_throw(function () { Assert::argument()->is_false(false); });
will_not_throw(function () { Assert::argument()->is_false(1 == 2); });
will_not_throw(function () { Assert::argument()->is_false(1 == 2)->is_false(false); });

will_throw(function () { Assert::argument()->is_false(0); });
will_throw(function () { Assert::argument()->is_false(new stdClass()); });
will_throw(function () { Assert::argument()->is_false(0.0); });
will_throw(function () { Assert::argument()->is_false(['hello' => 'world']); });
will_throw(function () { Assert::argument()->is_false(['hello']); });
will_throw(function () { Assert::argument()->is_false('o'); });
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
