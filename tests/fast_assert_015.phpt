--TEST--
Assert->is_not_null() function - basic test to ensure that is_not_null works as expected
--FILE--
<?php
require_once "test_helper.php";
will_not_throw(function () { Assert::argument()->is_not_null(false); });
will_not_throw(function () { Assert::argument()->is_not_null(123456); });
will_not_throw(function () { Assert::argument()->is_not_null(new stdClass()); });
will_not_throw(function () { Assert::argument()->is_not_null(0.0); });
will_not_throw(function () { Assert::argument()->is_not_null(['hello' => 'world']); });
will_not_throw(function () { Assert::argument()->is_not_null(['hello']); });
will_not_throw(function () { Assert::argument()->is_not_null('o'); });
will_not_throw(function () { Assert::argument()->is_not_null(2)->is_not_null('woohoo'); });

will_throw(function () { Assert::argument()->is_not_null(null); });
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
