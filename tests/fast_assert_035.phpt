--TEST--
Assert->is_empty() function - basic test to ensure that is_empty works as expected
--FILE--
<?php
require_once "test_helper.php";

function test_is_empty($a)
{
	if ( !empty($a) ) {
		try { Assert::argument()->is_empty($a); }
		catch (InvalidArgumentException $e) { echo "Right Value\n"; }
	}
	else {
		Assert::argument()->is_empty($a);
		echo "Right Value\n";
	}
}

test_is_empty(false);
test_is_empty(true);
test_is_empty(0.0);
test_is_empty(0);
test_is_empty(1.1);
test_is_empty(11);
test_is_empty([]);
test_is_empty(['abc']);
test_is_empty(new stdClass());
test_is_empty(null);
test_is_empty('0.0');
test_is_empty('0');
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
