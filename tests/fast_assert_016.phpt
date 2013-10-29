--TEST--
Assert->is_key_in_array() function - basic test to ensure that is_key_in_array works as expected
--FILE--
<?php
require_once "test_helper.php";

class TC {
	public $test_aarr = ['hello' => 'world', 'goodnight' => 'moon', 1234 => 321];
	public $test_arr = [1, 2, 3, 4, 5];
	public function runtests() {
		will_not_throw(function () { Assert::argument()->is_key_in_array('hello', $this->test_aarr); });
		will_not_throw(function () { Assert::argument()->is_key_in_array("goodnight", $this->test_aarr); });
		will_not_throw(function () { Assert::argument()->is_key_in_array(1234, $this->test_aarr); });
		will_not_throw(function () { Assert::argument()->is_key_in_array(0, $this->test_arr); });
		will_not_throw(function () { Assert::argument()->is_key_in_array(4, $this->test_arr); });

		will_throw(function () { Assert::argument()->is_key_in_array(5, $this->test_arr); });
		will_throw(function () { Assert::argument()->is_key_in_array(2.4, $this->test_aarr); });
		will_throw(function () { Assert::argument()->is_key_in_array(null, $this->test_aarr); });
		will_throw(function () { Assert::argument()->is_key_in_array('asdf', $this->test_aarr); });
		will_throw(function () { Assert::argument()->is_key_in_array('asdf', 'notanarray'); });
	}
}
$tester = new TC();
$tester->runtests();
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
Exception thrown
