--TEST--
Assert->is_scalar() function - basic test to ensure that is_scalar works as expected
--FILE--
<?php
require_once "test_helper.php";
will_not_throw(function () { Assert::argument()->is_scalar(123456); });
will_not_throw(function () { Assert::argument()->is_scalar(0.0); });
will_not_throw(function () { Assert::argument()->is_scalar("3.14159"); });
will_not_throw(function () { Assert::argument()->is_scalar('-14159'); });
will_not_throw(function () { Assert::argument()->is_scalar(2)->is_scalar('123'); });
will_not_throw(function () { Assert::argument()->is_scalar(false); });
will_not_throw(function () { Assert::argument()->is_scalar('o'); });

will_throw(function () { Assert::argument()->is_scalar(new stdClass()); });
will_throw(function () { Assert::argument()->is_scalar(['hello' => 'world']); });
will_throw(function () { Assert::argument()->is_scalar(['hello']); });
will_throw(function () { Assert::argument()->is_scalar(null); });
?>
--EXPECT--
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
