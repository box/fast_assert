--TEST--
Assert->is_callable() function - basic test to ensure that is_callable works as expected
--FILE--
<?php
require_once "test_helper.php";
will_throw(function () { Assert::argument()->is_callable(123456); });
will_throw(function () { Assert::argument()->is_callable(0.0); });
will_throw(function () { Assert::argument()->is_callable("3.14159"); });
will_throw(function () { Assert::argument()->is_callable('-14159'); });
will_throw(function () { Assert::argument()->is_callable(null); });
will_throw(function () { Assert::argument()->is_callable(false); });
will_throw(function () { Assert::argument()->is_callable(new stdClass()); });
will_throw(function () { Assert::argument()->is_callable(['hello' => 'world']); });
will_throw(function () { Assert::argument()->is_callable(['hello']); });
will_throw(function () { Assert::argument()->is_callable('o'); });

$a = function () { $b = 1; };
will_not_throw(function () { Assert::argument()->is_callable(function () {}); });
will_not_throw(function () use ($a) { Assert::argument()->is_callable($a); });
will_not_throw(function () use ($a) { Assert::argument()->is_callable($a)->is_callable(function () {}); });
?>
--EXPECT--
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
No exception thrown
No exception thrown
No exception thrown
