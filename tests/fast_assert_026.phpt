--TEST--
Assert->is_instance_of() function - basic test to ensure that is_instance_of works as expected
--FILE--
<?php
require_once "test_helper.php";
class A {
	public function foo() {}
}
class B implements I {
	public function bar() {}
}
interface I {
	public function bar();
}
class C extends B {
	public function fooBAR() {}
}

$a = new A();
$b = new B();
$c = new C();

will_not_throw(function () use ($a) { Assert::argument()->is_instance_of($a, 'A'); });
will_not_throw(function () use ($b) { Assert::argument()->is_instance_of($b, 'B'); });
will_not_throw(function () use ($b) { Assert::argument()->is_instance_of($b, 'I'); });
will_not_throw(function () use ($c) { Assert::argument()->is_instance_of($c, 'I'); });
will_not_throw(function () use ($c) { Assert::argument()->is_instance_of($c, 'B'); });

will_throw(function () { Assert::argument()->is_instance_of(null, null); });
will_throw(function () use ($a) { Assert::argument()->is_instance_of($a, 'fake123'); });
will_throw(function () use ($a) { Assert::argument()->is_instance_of($a, 'B'); });
will_throw(function () use ($a) { Assert::argument()->is_instance_of($a, 'I'); });
?>
--EXPECT--
No exception thrown
No exception thrown
No exception thrown
No exception thrown
No exception thrown
Exception thrown
Exception thrown
Exception thrown
Exception thrown
