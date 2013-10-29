--TEST--
Assert::received_value() function - basic test to ensure that Assert::received_value works as expected
--FILE--
<?php
var_dump(is_object(Assert::received_value()));
var_dump(is_object(Assert::received_value()->is_true(true)->is_true(2==2)));
try {
	Assert::received_value()->is_true(false);
}
catch (UnexpectedValueException $e)
{
	var_dump($e->getCode());
	echo $e->getMessage()."\n";
}
?>
--EXPECT--
bool(true)
bool(true)
int(0)
The statement (bool) false was not true
