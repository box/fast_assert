--TEST--
Assert->is_truthy() function - basic test to ensure that is_truthy works as expected
--FILE--
<?php
require_once "test_helper.php";
will_not_throw(function () { Assert::argument()->is_truthy(true); });
will_not_throw(function () { Assert::argument()->is_truthy(1 == 1); });
will_not_throw(function () { Assert::argument()->is_truthy(1 == 1)->is_truthy(true); });
will_not_throw(function () { Assert::argument()->is_truthy(1); });
will_not_throw(function () { Assert::argument()->is_truthy(new stdClass()); });
will_not_throw(function () { Assert::argument()->is_truthy(3.14159); });
will_not_throw(function () { Assert::argument()->is_truthy(['hello' => 'world']); });
will_not_throw(function () { Assert::argument()->is_truthy(['hello']); });
will_not_throw(function () { Assert::argument()->is_truthy('o'); });
will_not_throw(function () { Assert::argument()->is_truthy('0.0'); });
will_throw(function () { Assert::argument()->is_truthy(false); });
will_throw(function () { Assert::argument()->is_truthy(0); });
will_throw(function () { Assert::argument()->is_truthy(0.0); });
will_throw(function () { Assert::argument()->is_truthy([]); });
will_throw(function () { Assert::argument()->is_truthy(false)->is_truthy(true); });
will_throw(function () { Assert::argument()->is_truthy(null); });
will_throw(function () { Assert::argument()->is_truthy(''); });
will_throw(function () { Assert::argument()->is_truthy('0'); });
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
