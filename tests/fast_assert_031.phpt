--TEST--
Assert with test - assure that Assert::with() works properly
--FILE--
<?php
class MyNewAwesomeException extends Exception
{}

try { Assert::with('Exception')->is_true(false); }
catch (Exception $e) { echo $e->getMessage()."\n"; }

try { Assert::with('MyNewAwesomeException')->is_true(false); }
catch (MyNewAwesomeException $e) { echo $e->getMessage()."\n"; }

?>
--EXPECT--
The statement (bool) false was not true
The statement (bool) false was not true
