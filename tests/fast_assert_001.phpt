--TEST--
Assert::argument() function - basic test to ensure that Assert::argument works as expected
--FILE--
<?php
var_dump(is_object(Assert::argument()));
var_dump(is_object(Assert::argument()->is_true(true)->is_true(2==2)));
try {
	Assert::argument()->is_true(false);
}
catch (InvalidArgumentException $e)
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
