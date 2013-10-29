--TEST--
Assert->is_not_empty() function - basic test to ensure that is_not_empty works as expected
--FILE--
<?php
require_once "test_helper.php";

function test_is_not_empty($a)
{
	if ( empty($a) ) {
		try { Assert::argument()->is_not_empty($a); }
		catch (InvalidArgumentException $e) { echo "Right Value\n"; }
	}
	else {
		Assert::argument()->is_not_empty($a);
		echo "Right Value\n";
	}
}

test_is_not_empty(false);
test_is_not_empty(true);
test_is_not_empty(0.0);
test_is_not_empty(0);
test_is_not_empty(1.1);
test_is_not_empty(11);
test_is_not_empty([]);
test_is_not_empty(['abc']);
test_is_not_empty(new stdClass());
test_is_not_empty(null);
test_is_not_empty('0.0');
test_is_not_empty('0');
?>
--EXPECT--
Right Value
Right Value
Right Value
Right Value
Right Value
Right Value
Right Value
Right Value
Right Value
Right Value
Right Value
Right Value
