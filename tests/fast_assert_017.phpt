--TEST--
Assert->is_numeric() function - basic test to ensure that is_numeric works as expected
--FILE--
<?php
require_once "test_helper.php";
will_not_throw(function () { Assert::argument()->is_numeric(123456); });
will_not_throw(function () { Assert::argument()->is_numeric(0.0); });
will_not_throw(function () { Assert::argument()->is_numeric("3.14159"); });
will_not_throw(function () { Assert::argument()->is_numeric('-14159'); });
will_not_throw(function () { Assert::argument()->is_numeric(2)->is_numeric('123'); });

will_throw(function () { Assert::argument()->is_numeric(null); });
will_throw(function () { Assert::argument()->is_numeric(false); });
will_throw(function () { Assert::argument()->is_numeric(new stdClass()); });
will_throw(function () { Assert::argument()->is_numeric(['hello' => 'world']); });
will_throw(function () { Assert::argument()->is_numeric(['hello']); });
will_throw(function () { Assert::argument()->is_numeric('o'); });
?>
--EXPECT--
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
