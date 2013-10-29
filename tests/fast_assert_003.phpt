--TEST--
Assert::logic() function - basic test to ensure that Assert::logic works as expected
--FILE--
<?php
var_dump(is_object(Assert::logic()));
var_dump(is_object(Assert::logic()->is_true(true)->is_true(2==2)));
try {
	Assert::logic()->is_true(false);
}
catch (LogicException $e)
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
