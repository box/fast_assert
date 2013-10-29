--TEST--
Assert->is_integery() function - basic test to ensure that is_integery works as expected
--FILE--
<?php
require_once "test_helper.php";
will_not_throw(function () { Assert::argument()->is_integery(123456); });
will_not_throw(function () { Assert::argument()->is_integery(0.0); });
will_not_throw(function () { Assert::argument()->is_integery(123532.0); });
will_not_throw(function () { Assert::argument()->is_integery('-14159'); });
will_not_throw(function () { Assert::argument()->is_integery(2)->is_integery('123'); });
will_not_throw(function () { Assert::argument()->is_integery('-14.0'); });

will_throw(function () { Assert::argument()->is_integery("3.14159"); });
will_throw(function () { Assert::argument()->is_integery(3.14159); });
will_throw(function () { Assert::argument()->is_integery('-14.1'); });
will_throw(function () { Assert::argument()->is_integery(null); });
will_throw(function () { Assert::argument()->is_integery(false); });
will_throw(function () { Assert::argument()->is_integery(new stdClass()); });
will_throw(function () { Assert::argument()->is_integery(['hello' => 'world']); });
will_throw(function () { Assert::argument()->is_integery(['hello']); });
will_throw(function () { Assert::argument()->is_integery('o'); });
will_throw(function () { Assert::argument()->is_integery('1e6'); });
will_throw(function () { Assert::argument()->is_integery('1e0'); });
?>
--EXPECT--
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
Exception thrown
Exception thrown
Exception thrown
