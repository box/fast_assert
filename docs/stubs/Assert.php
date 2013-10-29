<?php

/**
* Stub documentation for the Assert extension
*/
class Assert
{
	/**
	 * @static
	 * @return Assert - this will throw an InvalidArgumentException if any subsequent instance method calls fail
	 */
	public static function argument() {}

	/**
	 * @static
	 * @return Assert - this will throw an UnexpectedValueException if any subsequent instance method calls fail
	 */
	public static function received_value() {}

	/**
	 * @static
	 * @return Assert - this will throw an LogicException if any subsequent instance method calls fail
	 */
	public static function logic() {}

	/**
	 * @static
	 * @param string $exception_name
	 * @return Assert - this will throw an $exception_name if any subsequent instance method calls fail
	 * @throws InvalidArgumentException if $exception_name doesn't refer to a class that is a subclass of Exception
	 * WARNING - this method is much slower than ::argument, received_value, and logic - use with caution
	 */
	public static function with($exception_name) {}

	/**
	 * Asserts that $statement === true
	 * @param $statement
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_true($statement, $message="") {}

	/**
	 * Asserts that $statement == true
	 * @param $statement
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_truthy($statement, $message="") {}

	/**
	 * Asserts that $statement === false
	 * @param $statement
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_false($statement, $message="") {}

	/**
	 * Asserts that is_numeric($thing) == true
	 * @param $thing
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_numeric($thing, $message="") {}

	/**
	 * Asserts that is_numeric($thing) != true
	 * @param $thing
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_not_numeric($thing, $message="") {}

	/**
	 * Raises an exception if the $thing's value is not numeric and is not equal to its value cast to an int. This
	 * allows strings that represent integers but disallows strings that represent floats.
	 *
	 * NOTE: This method also disallows any strings of numbers in scientific notation (i.e. 1.2e6), because php handles
	 * them in a completely braindead way and letting them pass this function would be dangerous. For example,
	 * (int) '1e6' == 1, but '1e6' + 0 == 1000000
	 *
	 * @param $thing mixed
	 * @param $message string message
	 * @return Assert
	 */
	public function is_integery($thing, $message="") {}

	/**
	 * Asserts that is_integer($thing) == true
	 * @param $thing
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_integer($thing, $message="") {}

	/**
	 * Asserts that is_float($thing) == true
	 * @param $thing
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_float($thing, $message="") {}

	/**
	 * Asserts that is_array($thing) == true
	 * @param $thing
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_array($thing, $message="") {}

	/**
	 * Asserts that $thing is an associative array. For $thing to not be an associative array,
	 * all array keys must be in order integers increasing from 0. An equivalent way to test this
	 * in php is $is_assoc_array = (array_keys($thing) == array_keys(array_keys($thing)))
	 * @param $thing
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_associative_array($thing, $message="") {}

	/**
	 * Asserts that is_scalar($thing) == true
	 * @param mixed $thing
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_scalar($thing, $message="") {}

	/**
	 * Asserts that is_object($thing) == true
	 * @param $thing
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_object($thing, $message="") {}

	/**
	 * Asserts that is_string($thing) == true
	 * @param $thing
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_string($thing, $message="") {}

	/**
	 * Asserts that is_bool($thing) == true
	 * @param $thing
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_boolean($thing, $message="") {}

	/**
	 * Asserts that $thing != null
	 * @param $thing
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_not_null($thing, $message="") {}

	/**
	 * Asserts that empty($thing) == false
	 * @param $thing
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_not_empty($thing, $message="") {}

	/**
	 * Asserts that empty($thing) == true
	 * @param $thing
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_empty($thing, $message="") {}

	/**
	 * Asserts that is_callable($thing) == true
	 * @param $thing
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_callable($thing, $message="") {}

	/**
	 * Asserts that the value $value is in the array $array
	 * @param Array $array
	 * @param Mixed $value
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_in_array($array, $value, $message="") {}

	/**
	 * Asserts $array contains the key $key
	 *
	 * @param mixed $key Key to ensure exists
	 * @param array $array
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_key_in_array($key, array $array, $message="") {}

	/**
	 * Asserts that $expected === $actual
	 * @param $expected - The expected value.
	 * @param $actual - The actual value. Note that if you are testing equality against an expected string type you
	 * should ALWAYS use are_same() instead of are_equal() due to the bug-prone nature of comparison between
	 * strings and integers.
	 * @param string|void $message
	 * @return Assert
	 */
	public function are_equal($expected, $actual, $message="") {}

	/**
	 * Asserts that $expected !== $actual
	 * @param $expected - The expected value.
	 * @param $actual - The actual value.
	 * @param string|void $message
	 * @return Assert
	 */
	public function are_not_equal($expected, $actual, $message="") {}

	/**
	 * Asserts that $expected == $actual
	 * @param $expected - The expected value.
	 * @param $actual - The actual value.
	 * @param string|void $message
	 * @return Assert
	 */
	public function are_same($expected, $actual, $message="") {}

	/**
	 * Asserts that $expected != $actual
	 * @param $expected - The expected value.
	 * @param $actual - The actual value.
	 * @param string|void $message
	 * @return Assert
	 */
	public function are_not_same($expected, $actual, $message="") {}

	/**
	 * Asserts that is_a($instance, $expected) == true
	 * @param $instance
	 * @param $expected string name of class
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_instance_of($instance, $expected, $message="") {}

	/**
	 * Asserts that $classname is a defined classname
	 * @param $classname
	 * @param string|void $message
	 * @return Assert
	 */
	public function is_classname($classname, $message="") {}

	/**
	 * Asserts that $object uses the trait $trait_name
	 * NOTE: This only asserts that the $object's class itself uses $trait_name -
	 * if a superclass of $object uses the trait $trait_name but not the $object this will
	 * throw an exception
	 *
	 * @param $object
	 * @param string $trait_name
	 * @param string|void $message
	 * @return Assert
	 */
	public function uses_trait($object, $trait_name, $message="") {}
}
