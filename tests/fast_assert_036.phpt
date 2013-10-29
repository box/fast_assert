--TEST--
Basic test to ensure that you can't make a new assert object with its controller
--FILE--
<?php

try { $a = new Assert(); }
catch (BadMethodCallException $e) { echo $e->getMessage()."\n"; }
?>
--EXPECT--
It is forbidden to call Asserts constructor
