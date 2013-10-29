--TEST--
Basic test of error handling when passing in 0 and 1 args to an Assert method that
takes two
--FILE--
<?php
try { Assert::argument()->are_same(1); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }
try { Assert::argument()->are_same(); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }
?>
--EXPECTREGEX--
Warning: Assert::are_same\(\) expects at least 2 parameters, 1 given(.*)
The values \(no statement was passed in!\) and \(no statement was passed in!\) are not identical

Warning: Assert::are_same\(\) expects at least 2 parameters, 0 given(.*)
The values \(no statement was passed in!\) and \(no statement was passed in!\) are not identical
