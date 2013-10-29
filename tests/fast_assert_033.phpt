--TEST--
Assert->is_in_array() function - basic test to ensure that is_in_array works as expected
--FILE--
<?php
require_once "test_helper.php";

will_throw(function () { Assert::argument()->is_in_array(['a','b','c'], 'd'); });
will_throw(function () { Assert::argument()->is_in_array([0 => 'a',1 => 'b', 2 =>'c'], []); });
will_throw(function () { Assert::argument()->is_in_array('notanarraylol', 'a'); });
will_throw(function () { Assert::argument()->is_in_array([], 'b'); });

will_not_throw(function () { Assert::argument()->is_in_array([0 => 'a',1 => 'b', 3 =>'c'], 'a'); });
will_not_throw(function () { Assert::argument()->is_in_array(['a','b','c'], 'a'); });
will_not_throw(function () { Assert::argument()->is_in_array([0 => 'a', 'b' => ['c']], ['c']); });
will_not_throw(function () { Assert::argument()->is_in_array([0 => 'a'], 'a')->is_in_array(['hello' => 'world'], 'world'); });
?>
--EXPECT--
Exception thrown
Exception thrown
Exception thrown
Exception thrown
No exception thrown
No exception thrown
No exception thrown
No exception thrown
