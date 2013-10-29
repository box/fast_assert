--TEST--
Assert->uses_trait() function - basic test to ensure that uses_trait works as expected
--FILE--
<?php
require_once "test_helper.php";
trait T {
	public function baz() {}
}
trait S {
	public function bazzing() {}
}
class A {
	use T;
	public function foo() {}
}
class B implements I {
	use T;
	public function bar() {}
}
interface I {
	public function bar();
}
class C extends B {
	use S;
	public function fooBAR() {}
}

$a = new A();
$b = new B();
$c = new C();

will_not_throw(function () use ($a) { Assert::argument()->uses_trait($a, 'T'); });
will_not_throw(function () use ($b) { Assert::argument()->uses_trait($b, 'T'); });
//will_not_throw(function () use ($c) { Assert::argument()->uses_trait($c, 'T'); });
will_not_throw(function () use ($c) { Assert::argument()->uses_trait($c, 'S')->uses_trait($c, 'S'); });

will_throw(function () { Assert::argument()->uses_trait(null, null); });
will_throw(function () use ($a) { Assert::argument()->uses_trait($a, 'fake123'); });
will_throw(function () use ($a) { Assert::argument()->uses_trait($a, 'S'); });
will_throw(function () use ($b) { Assert::argument()->uses_trait($b, 'S'); });
will_throw(function () use ($c) { Assert::argument()->uses_trait($c, 'T'); });
?>
--EXPECT--
No exception thrown
No exception thrown
No exception thrown
Exception thrown
Exception thrown
Exception thrown
Exception thrown
Exception thrown
