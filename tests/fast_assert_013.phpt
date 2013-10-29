--TEST--
Assert->is_string() function - basic test to ensure that is_string works as expected
--FILE--
<?php
require_once "test_helper.php";
will_not_throw(function () { Assert::argument()->is_string('abc'); });
will_not_throw(function () { Assert::argument()->is_string("123"); });
will_not_throw(function () { Assert::argument()->is_string('baby')->is_string("you and me!"); });

will_throw(function () { Assert::argument()->is_string(false); });
will_throw(function () { Assert::argument()->is_string(null); });
will_throw(function () { Assert::argument()->is_string(new stdClass()); });
will_throw(function () { Assert::argument()->is_string(0.0); });
will_throw(function () { Assert::argument()->is_string(['hello' => 'world']); });
will_throw(function () { Assert::argument()->is_string(['hello']); });
will_throw(function () { Assert::argument()->is_string(42); });
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
