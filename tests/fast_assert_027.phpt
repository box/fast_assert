--TEST--
Assert->is_classname() function - basic test to ensure that is_classname works as expected
--FILE--
<?php
require_once "test_helper.php";
trait T {
	public function baz() {}
}
class A {
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
	public function fooBAR() {}
}
abstract class Z {
	abstract public function implementME();
}
class D extends Z {
	public function implementME() {}
}

will_not_throw(function () { Assert::argument()->is_classname('A'); });
will_not_throw(function () { Assert::argument()->is_classname('B'); });
will_not_throw(function () { Assert::argument()->is_classname('I'); });
will_not_throw(function () { Assert::argument()->is_classname('D'); });
will_not_throw(function () { Assert::argument()->is_classname('Z'); });

will_throw(function () { Assert::argument()->is_classname(null); });
will_throw(function () { Assert::argument()->is_classname('fake123'); });
will_throw(function () { Assert::argument()->is_classname('T'); });
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
