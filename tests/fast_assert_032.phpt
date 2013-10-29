--TEST--
Assert->is_associative_array() function - basic test to ensure that is_associative_array works as expected
--FILE--
<?php
require_once "test_helper.php";

will_throw(function () { Assert::argument()->is_associative_array(['a','b','c']); });
will_throw(function () { Assert::argument()->is_associative_array([0 => 'a',1 => 'b', 2 =>'c']); });
will_throw(function () { Assert::argument()->is_associative_array('notanarraylol'); });
will_throw(function () { Assert::argument()->is_associative_array([]); });

will_not_throw(function () { Assert::argument()->is_associative_array([0 => 'a',1 => 'b', 3 =>'c']); });
will_not_throw(function () { Assert::argument()->is_associative_array([1 => 'a',2 => 'b', 0 =>'c']); });
will_not_throw(function () { Assert::argument()->is_associative_array([0 => 'a', 'b' => 'c']); });
will_not_throw(function () { Assert::argument()->is_associative_array([0 => 'a', 'b' => 'c'])->is_associative_array(['hello' => 'world']); });
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
