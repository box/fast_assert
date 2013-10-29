--TEST--
Assert->is_object() function - basic test to ensure that is_object works as expected
--FILE--
<?php
class FakeClass {
	public function foo() { echo "bar"; }
}
require_once "test_helper.php";
will_not_throw(function () { Assert::argument()->is_object(new stdClass()); });
will_not_throw(function () { Assert::argument()->is_object(new FakeClass()); });
will_not_throw(function () { Assert::argument()->is_object(new stdClass())->is_object(new FakeClass()); });

will_throw(function () { Assert::argument()->is_object(false); });
will_throw(function () { Assert::argument()->is_object(null); });
will_throw(function () { Assert::argument()->is_object(42); });
will_throw(function () { Assert::argument()->is_object(0.0); });
will_throw(function () { Assert::argument()->is_object(['hello' => 'world']); });
will_throw(function () { Assert::argument()->is_object(['hello']); });
will_throw(function () { Assert::argument()->is_object('o'); });
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
