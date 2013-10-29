--TEST--
Assert->is_not_numeric() function - basic test to ensure that is_not_numeric works as expected
--FILE--
<?php
require_once "test_helper.php";
will_throw(function () { Assert::argument()->is_not_numeric(123456); });
will_throw(function () { Assert::argument()->is_not_numeric(0.0); });
will_throw(function () { Assert::argument()->is_not_numeric("3.14159"); });
will_throw(function () { Assert::argument()->is_not_numeric('-14159'); });
will_throw(function () { Assert::argument()->is_not_numeric(2)->is_not_numeric('123'); });

will_not_throw(function () { Assert::argument()->is_not_numeric(null); });
will_not_throw(function () { Assert::argument()->is_not_numeric(false); });
will_not_throw(function () { Assert::argument()->is_not_numeric(new stdClass()); });
will_not_throw(function () { Assert::argument()->is_not_numeric(['hello' => 'world']); });
will_not_throw(function () { Assert::argument()->is_not_numeric(['hello']); });
will_not_throw(function () { Assert::argument()->is_not_numeric('o'); });
will_not_throw(function () { Assert::argument()->is_not_numeric(null)->is_not_numeric(false); });
?>
--EXPECT--
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
No exception thrown
