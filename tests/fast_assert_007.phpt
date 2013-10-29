--TEST--
Assert error message test - assure that different objects generate appropriate error messages
--FILE--
<?php
try { Assert::argument()->is_true(false); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_true(null); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_true(1234); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_true(3.141592); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_true("hello world"); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_true(['hello']); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_true(['hello' => 'world']); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_true(new stdClass()); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

try { Assert::argument()->is_true(); }
catch (InvalidArgumentException $e) { echo $e->getMessage()."\n"; }

?>
--EXPECTREGEX--
The statement \(bool\) false was not true
The statement null was not true
The statement \(int\) 1234 was not true
The statement \(float\) 3.141592 was not true
The statement \(string\) hello world was not true
The statement Array was not true
The statement Array was not true
The statement Object was not true

Warning: Assert::is_true\(\) expects at least 1 parameter, 0 given(.*)
The statement \(no statement was passed in!\) was not true
