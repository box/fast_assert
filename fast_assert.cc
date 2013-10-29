/*
 * The single point of entry for all of the Assert class
 * and it's functionality
 *
 * Author: Joseph Marrama <jmarrama@box.com>
 * Copyright (c) Box, Inc. 2013
 */

#include "php_fast_assert.h"
#include "zend_exceptions.h"
#include "zend_operators.h"
#include "ext/spl/spl_exceptions.h"

#define likely(x) __builtin_expect((x),1)
#define unlikely(x) __builtin_expect((x),0)

const char *IS_TRUE_ERR = "The statement %s was not true";
const char *IS_TRUTHY_ERR = "The statement %s was not truthy";
const char *IS_EMPTY_ERR = "The value %s is not empty";
const char *IS_NOT_EMPTY_ERR = "The value %s is empty";
const char *IS_FALSE_ERR = "The statement %s was not false";
const char *IS_INTEGER_ERR = "The value %s is not an integer";
const char *IS_FLOAT_ERR = "The value %s is not a float";
const char *IS_ARRAY_ERR = "The value %s is not an array";
const char *IS_OBJECT_ERR = "The value %s is not an object";
const char *IS_STRING_ERR = "The value %s is not a string";
const char *IS_BOOLEAN_ERR = "The value %s is not a boolean";
const char *IS_NOT_NULL_ERR = "The given value should not have been %s";
const char *IS_KEY_IN_ARRAY_ERR = "The key %s was not in %s";
const char *IS_KEY_IN_ARRAY_INVALID_ARR_ERR = "Argument 2 passed to is_key_in_array is %s, not an array";
const char *IS_NUMERIC_ERR = "The value %s is not numeric";
const char *IS_NOT_NUMERIC_ERR = "The value %s is numeric";
const char *IS_INTEGERY_ERR = "The value %s does not represent an integer";
const char *IS_SCALAR_ERR = "The value %s is not a scalar";
const char *ARE_SAME_ERR = "The values %s and %s are not identical";
const char *ARE_NOT_SAME_ERR = "The values %s and %s are identical";
const char *ARE_EQUAL_ERR = "The values %s and %s are not equal";
const char *ARE_NOT_EQUAL_ERR = "The values %s and %s are equal";
const char *IS_CALLABLE_ERR = "The value %s is not callable";
const char *IS_INSTANCE_OF_ERR = "The value %s is not an instance of %s";
const char *IS_CLASSNAME_ERR = "The value %s is not a class name";
const char *USES_TRAIT_ERR = "The value %s does not use the trait %s";
const char *IS_ASSOC_ARR_ERR = "The value %s is not an associative array";
const char *IS_IN_ARRAY_ERR = "The value %s is not in the array %s";
const char *WITH_NO_ARG_ERR = "You must pass an argument to Assert::with!";
const char *WITH_NO_STRING_ERR = "You must pass a string to Assert::with!";
const char *WITH_INVALID_CLASSNAME_ERR = "You must pass a valid classname to Assert::with!";
const char *WITH_NOT_EXCEPTION_SUBCLASS_ERR = "The classname passed to Assert::with is not a subclass of Exception!";

zend_class_entry *fast_assert_ce;
zend_object_handlers fake_assert_obj_handlers;

/* Global storage */
#ifdef ZTS
int fa_globals_id;
#else
fast_assert_globals fa_globals;
#endif /* ZTS */

/**
 * Helper macros to extract params from assert's instance method calls
 */
#define ASSERT_EXTRACT_ONE_PARAM(errstring, arg, err_msg) do { 										\
	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z|z", &arg, &err_msg) == FAILURE) { 		\
		assert_throw_exception_on_unary_assertion(getThis(), errstring, NULL, err_msg TSRMLS_CC); 	\
		return; 																					\
	} 																								\
} while(0)

#define ASSERT_EXTRACT_TWO_PARAMS(errstring, arg, arg2, err_msg) do { 											\
	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "zz|z", &arg, &arg2, &err_msg) == FAILURE) {			\
		assert_throw_exception_on_binary_assertion(getThis(), errstring, NULL, NULL, err_msg TSRMLS_CC); 		\
		return; 																								\
	} 																											\
} while (0)

/*
 * Helper function to print an appropriate error string to strbuf using the fmt string passed in and
 * the value of err_val
 * NOTE: this unfortunately duplicates lots of logic from 'zend_make_printable_zval', due to the requirement
 * that this function not malloc any memory and not raise spurious E_NOTICE warnings
 */
static void debug_print_zval(char *strbuf, const char* err_fmt_string, zval *err_val, size_t maxlen)
{
	// buffer to store any intermediate representations of values
	char intstrbuf[512];

	// short circuit if err_val is NULL (happens when no arg is passed in)
	if (!err_val) {
		snprintf(strbuf, maxlen, err_fmt_string, "(no statement was passed in!)");
		return;
	}

	switch (Z_TYPE_P(err_val)) {
	case IS_NULL:
		snprintf(strbuf, maxlen, err_fmt_string, "null");
		return;
	case IS_BOOL:
		if (Z_LVAL_P(err_val)) {
			snprintf(strbuf, maxlen, err_fmt_string, "(bool) true");
		} else {
			snprintf(strbuf, maxlen, err_fmt_string, "(bool) false");
		}
		return;
	case IS_STRING:
		snprintf(intstrbuf, 512, "(string) %s", Z_STRVAL_P(err_val));
		snprintf(strbuf, maxlen, err_fmt_string, intstrbuf);
		return;
	case IS_LONG:
		snprintf(intstrbuf, 512, "(int) %d", Z_LVAL_P(err_val));
		snprintf(strbuf, maxlen, err_fmt_string, intstrbuf);
		return;
	case IS_DOUBLE:
		snprintf(intstrbuf, 512, "(float) %f", Z_DVAL_P(err_val));
		snprintf(strbuf, maxlen, err_fmt_string, intstrbuf);
		return;
	case IS_ARRAY:
		snprintf(strbuf, maxlen, err_fmt_string, "Array");
		return;
	case IS_OBJECT:
		snprintf(strbuf, maxlen, err_fmt_string, "Object");
		return;
	case IS_RESOURCE:
		snprintf(strbuf, maxlen, err_fmt_string, "Resource");
		return;
	default:
		snprintf(strbuf, maxlen, err_fmt_string, "Unknown");
	}
}

/**
 * Helper method to dump a nice error string into strbuf. This bacially does
 * (err_fmt_string % (pretty_print(err_val))) + ", " + err_msg
 */
static void get_good_error_string(char *strbuf, const char* err_fmt_string, zval *err_val, zval *err_msg, size_t maxlen)
{
	int str_len;
	memset((void *)strbuf, 0, maxlen);
	debug_print_zval(strbuf, err_fmt_string, err_val, maxlen);
	if (err_msg && Z_TYPE_P(err_msg) == IS_STRING) {
		str_len = strlen(strbuf); // NOTE: str_len does not include the null terminating char
		strncat(strbuf, ", ", maxlen - str_len - 1);
		strncat(strbuf, Z_STRVAL_P(err_msg), maxlen - str_len - 3);
	}
}

/**
 * Helper method to throw an exception on a 'generic' assert object - i.e. one made with a custom
 * exception type
 */
static void throw_exception_using_generic_assert_obj(zval* assert_obj, char *message TSRMLS_DC)
{
	zval *classname;
	zend_class_entry **ce;
	classname = zend_read_property(fast_assert_ce, assert_obj, "exception_type", sizeof("exception_type") - 1, 1 TSRMLS_CC);
	zend_lookup_class(Z_STRVAL_P(classname), Z_STRLEN_P(classname), &ce TSRMLS_CC);
	zend_throw_exception(*ce, message, 0 TSRMLS_CC);
}

/**
 * Helper method to throw an exception. Uses the assert_obj zval to decide which type of exception to throw, and
 * populates the message of the exception by doing (err_fmt_string % (pretty_print(err_val))) + ", " + err_msg
 */
static void assert_throw_exception_on_unary_assertion(zval *assert_obj, const char *err_fmt_string, zval *err_val, zval* err_msg TSRMLS_DC)
{
	// string buffer for storing the exception string
	// NOTE: zend_throw_exception copies the string a few function calls down, so we can use stack memory here
	char strbuf[1024];
	get_good_error_string(strbuf, err_fmt_string, err_val, err_msg, 1024);

	// switch on the handle of the object passed in
	zend_object_handle obj_handle = Z_OBJ_HANDLE_P(assert_obj);

	if (obj_handle == AssertGlobals(invalid_arg_assert_handle)) {
		zend_throw_exception(spl_ce_InvalidArgumentException, strbuf, 0 TSRMLS_CC);
		return;
	} else if (obj_handle == AssertGlobals(unexpected_val_assert_handle)) {
		zend_throw_exception(spl_ce_UnexpectedValueException, strbuf, 0 TSRMLS_CC);
		return;
	} else if (obj_handle == AssertGlobals(logic_exception_assert_handle)) {
		zend_throw_exception(spl_ce_LogicException, strbuf, 0 TSRMLS_CC);
		return;
	}

	// if this wasn't a special object corresponding to an exception type,
	// throw the type of exception that the object contains
	throw_exception_using_generic_assert_obj(assert_obj, strbuf TSRMLS_CC);
	return;
}

/**
 * Helper method to throw an exception using 2 arguments, val1 and val2, in the error string.
 * NOTE: err_fmt_string must have two string format specifiers (i.e., two %s's)
 * PS: This method is a little bit of a hack in that it just makes a new format string with one %s
 * and passes it down to assert_throw_exception_on_unary_assertion. Yay code reuse!
 */
static void assert_throw_exception_on_binary_assertion(zval *assert_obj, const char *err_fmt_string, zval *val1, zval* val2, zval* err_msg TSRMLS_DC)
{
	char val1_print_buffer[512];
	char new_format_string[512];
	debug_print_zval(val1_print_buffer, "%s", val1, 512);
	snprintf(new_format_string, 512, err_fmt_string, val1_print_buffer, "%s");
	assert_throw_exception_on_unary_assertion(assert_obj, new_format_string, val2, err_msg TSRMLS_CC);
}

PHP_METHOD(Assert, __construct)
{
	char errbuf[128];
	strncpy(errbuf, "It is forbidden to call Asserts constructor", 128);
	zend_throw_exception(spl_ce_BadMethodCallException, errbuf, 0 TSRMLS_CC);
}

PHP_METHOD(Assert, is_true)
{
	zval* arg;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_ONE_PARAM(IS_TRUE_ERR, arg, err_msg);

	if (unlikely(Z_TYPE_P(arg) != IS_BOOL)) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_TRUE_ERR, arg, err_msg TSRMLS_CC);
		return;
	}
	if (unlikely(!(Z_LVAL_P(arg)))) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_TRUE_ERR, arg, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, is_truthy)
{
	zval *arg;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_ONE_PARAM(IS_TRUTHY_ERR, arg, err_msg);

	if (unlikely(!i_zend_is_true(arg))) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_TRUTHY_ERR, arg, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, is_empty)
{
	zval *arg;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_ONE_PARAM(IS_EMPTY_ERR, arg, err_msg);

	if (unlikely(i_zend_is_true(arg))) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_EMPTY_ERR, arg, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, is_not_empty)
{
	zval *arg;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_ONE_PARAM(IS_NOT_EMPTY_ERR, arg, err_msg);

	if (unlikely(!i_zend_is_true(arg))) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_NOT_EMPTY_ERR, arg, err_msg TSRMLS_CC);
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, is_false)
{
	zval* arg;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_ONE_PARAM(IS_FALSE_ERR, arg, err_msg);

	if (unlikely(Z_TYPE_P(arg) != IS_BOOL)) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_FALSE_ERR, arg, err_msg TSRMLS_CC);
		return;
	}
	if (unlikely(Z_LVAL_P(arg))) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_FALSE_ERR, arg, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, is_integer)
{
	zval* arg;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_ONE_PARAM(IS_INTEGER_ERR, arg, err_msg);

	if (unlikely(Z_TYPE_P(arg) != IS_LONG)) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_INTEGER_ERR, arg, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, is_float)
{
	zval* arg;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_ONE_PARAM(IS_FLOAT_ERR, arg, err_msg);

	if (unlikely(Z_TYPE_P(arg) != IS_DOUBLE)) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_FLOAT_ERR, arg, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, is_array)
{
	zval* arg;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_ONE_PARAM(IS_ARRAY_ERR, arg, err_msg);

	if (unlikely(Z_TYPE_P(arg) != IS_ARRAY)) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_ARRAY_ERR, arg, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, is_object)
{
	zval* arg;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_ONE_PARAM(IS_OBJECT_ERR, arg, err_msg);

	if (unlikely(Z_TYPE_P(arg) != IS_OBJECT)) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_OBJECT_ERR, arg, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, is_string)
{
	zval* arg;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_ONE_PARAM(IS_STRING_ERR, arg, err_msg);

	if (unlikely(Z_TYPE_P(arg) != IS_STRING)) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_STRING_ERR, arg, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, is_boolean)
{
	zval* arg;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_ONE_PARAM(IS_BOOLEAN_ERR, arg, err_msg);

	if (unlikely(Z_TYPE_P(arg) != IS_BOOL)) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_BOOLEAN_ERR, arg, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, is_not_null)
{
	zval* arg;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_ONE_PARAM(IS_NOT_NULL_ERR, arg, err_msg);

	if (unlikely(Z_TYPE_P(arg) == IS_NULL)) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_NOT_NULL_ERR, arg, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, is_key_in_array)
{
	zval *zarray = NULL;
	HashTable *array = NULL;
	zval* err_msg = NULL;
	zval *key = NULL;
	int key_exists = 0;
	ASSERT_EXTRACT_TWO_PARAMS(IS_KEY_IN_ARRAY_ERR, key, zarray, err_msg);

	// make sure the 2nd arg is an array
	if (Z_TYPE_P(zarray) != IS_ARRAY) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_KEY_IN_ARRAY_INVALID_ARR_ERR, zarray, err_msg TSRMLS_CC);
		return;
	}
	array = Z_ARRVAL_P(zarray);

	switch (Z_TYPE_P(key)) {
	case IS_STRING:
		if (zend_symtable_exists(array, Z_STRVAL_P(key), Z_STRLEN_P(key) + 1)) {
			key_exists = 1;
		}
		break;
	case IS_LONG:
		if (zend_hash_index_exists(array, Z_LVAL_P(key))) {
			key_exists = 1;
		}
		break;
	case IS_NULL:
		// this makes me sad, but this case is included to maintain completely parity with array_key_exists
		if (zend_hash_exists(array, "", 1)) {
			key_exists = 1;
		}
		break;
	}

	if (unlikely(!key_exists)) {
		assert_throw_exception_on_binary_assertion(getThis(), IS_KEY_IN_ARRAY_ERR, key, zarray, err_msg TSRMLS_CC);
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

static int is_numeric_helper(zval *val)
{
	switch (Z_TYPE_P(val)) {
	case IS_DOUBLE:
	case IS_LONG:
		return 1;
	case IS_STRING:
		return (is_numeric_string(Z_STRVAL_P(val), Z_STRLEN_P(val), NULL, NULL, 0)) ? 1 : 0;
	default:
		return 0;
	}
}

PHP_METHOD(Assert, is_numeric)
{
	zval* val;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_ONE_PARAM(IS_NUMERIC_ERR, val, err_msg);

	if (unlikely(!is_numeric_helper(val))) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_NUMERIC_ERR, val, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, is_not_numeric)
{
	zval* val;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_ONE_PARAM(IS_NOT_NUMERIC_ERR, val, err_msg);

	if (unlikely(is_numeric_helper(val))) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_NOT_NUMERIC_ERR, val, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

/**
 * Helper method that returns 1 if a string of length len has a character other than
 * '-', '.', or '0' to '9'
 */
static int string_contains_nonnumber_char(char *str, int len)
{
	int i;
	for (i = 0; i < len; i++) {
		if (str[i] < 45 || str[i] > 57 ) {
			return 1;
		}
	}
	return 0;
}

PHP_METHOD(Assert, is_integery)
{
	zval* val;
	long lval;
	double dval;
	zend_uchar retval;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_ONE_PARAM(IS_INTEGERY_ERR, val, err_msg);

	switch (Z_TYPE_P(val)) {
	case IS_LONG:
		break;
	case IS_DOUBLE:
		if (unlikely((Z_DVAL_P(val) - (long)Z_DVAL_P(val)) != 0)) {
			assert_throw_exception_on_unary_assertion(getThis(), IS_INTEGERY_ERR, val, err_msg TSRMLS_CC);
			return;
		}
		break;
	case IS_STRING:
		retval = is_numeric_string(Z_STRVAL_P(val), Z_STRLEN_P(val), &lval, &dval, 0);
		if (retval == IS_LONG) {
			break;
		}
		// NOTE: we explicity disallow exponential strings, because PHP handles them in a braindead,
		// idiotic manner. in php, (int) '1e6' == 1 and '1e6' + 0 == 1000000.
		if (retval == IS_DOUBLE &&
		        dval - (long)dval == 0 &&
		        !string_contains_nonnumber_char(Z_STRVAL_P(val), Z_STRLEN_P(val)))
		{
			break;
		}
		assert_throw_exception_on_unary_assertion(getThis(), IS_INTEGERY_ERR, val, err_msg TSRMLS_CC);
		return;
	default:
		assert_throw_exception_on_unary_assertion(getThis(), IS_INTEGERY_ERR, val, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, is_scalar)
{
	zval* val;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_ONE_PARAM(IS_SCALAR_ERR, val, err_msg);

	switch (Z_TYPE_P(val)) {
	case IS_BOOL:
	case IS_DOUBLE:
	case IS_LONG:
	case IS_STRING:
		break;
	default:
		assert_throw_exception_on_unary_assertion(getThis(), IS_SCALAR_ERR, val, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

/*
 * Helper function that simply returns 1 if the two zvals are identical
 * is_identical_function has a weird interface where it returns the result sometimes in the retval
 * or sometimes in a zval
 */
static int is_identical_helper(zval* arg1, zval* arg2 TSRMLS_DC)
{
	zval res;
	if (is_identical_function(&res, arg1, arg2 TSRMLS_CC) == FAILURE) {
		return 0;
	}
	return (int)Z_LVAL(res);
}

PHP_METHOD(Assert, are_same)
{
	zval* val1 = NULL;
	zval* val2 = NULL;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_TWO_PARAMS(ARE_SAME_ERR, val1, val2, err_msg);

	if (unlikely(!is_identical_helper(val1, val2 TSRMLS_CC))) {
		assert_throw_exception_on_binary_assertion(getThis(), ARE_SAME_ERR, val1, val2, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, are_not_same)
{
	zval* val1;
	zval* val2;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_TWO_PARAMS(ARE_NOT_SAME_ERR, val1, val2, err_msg);

	if (unlikely(is_identical_helper(val1, val2 TSRMLS_CC))) {
		assert_throw_exception_on_binary_assertion(getThis(), ARE_NOT_SAME_ERR, val1, val2, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, are_equal)
{
	zval* val1;
	zval* val2;
	zval* err_msg = NULL;
	zval res;
	ASSERT_EXTRACT_TWO_PARAMS(ARE_EQUAL_ERR, val1, val2, err_msg);

	if (unlikely(!fast_equal_function(&res, val1, val2 TSRMLS_CC))) {
		assert_throw_exception_on_binary_assertion(getThis(), ARE_EQUAL_ERR, val1, val2, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, are_not_equal)
{
	zval* val1;
	zval* val2;
	zval* err_msg = NULL;
	zval res;
	ASSERT_EXTRACT_TWO_PARAMS(ARE_NOT_EQUAL_ERR, val1, val2, err_msg);

	if (unlikely(fast_equal_function(&res, val1, val2 TSRMLS_CC))) {
		assert_throw_exception_on_binary_assertion(getThis(), ARE_NOT_EQUAL_ERR, val1, val2, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, is_callable)
{
	zval* val;
	zval* err_msg = NULL;
	ASSERT_EXTRACT_ONE_PARAM(IS_CALLABLE_ERR, val, err_msg);
	if (unlikely(!zend_is_callable_ex(val, NULL, 0, NULL, NULL, NULL, NULL TSRMLS_CC))) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_CALLABLE_ERR, val, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, is_instance_of)
{
	zval* obj;
	zval* classname;
	zval* err_msg = NULL;
	zend_class_entry **ce;
	zend_class_entry *instance_ce;
	ASSERT_EXTRACT_TWO_PARAMS(IS_INSTANCE_OF_ERR, obj, classname, err_msg);

	// if the correct args were passed in, the classname exists, and the object is an instance of classname, return successfully!
	if (likely(Z_TYPE_P(obj) == IS_OBJECT && Z_TYPE_P(classname) == IS_STRING)) {
		if (likely(zend_lookup_class(Z_STRVAL_P(classname), Z_STRLEN_P(classname), &ce TSRMLS_CC) == SUCCESS)) {
			if (likely(HAS_CLASS_ENTRY(*obj) && instanceof_function(Z_OBJCE_P(obj), *ce TSRMLS_CC))) {
				RETURN_ZVAL(getThis(), 1, 0);
			}
		}
	}

	assert_throw_exception_on_binary_assertion(getThis(), IS_INSTANCE_OF_ERR, obj, classname, err_msg TSRMLS_CC);
	return;
}

PHP_METHOD(Assert, is_classname)
{
	zval *val;
	zval* err_msg = NULL;
	zend_class_entry **ce;
	zend_bool class_exists_retval;
	zend_bool iface_exists_retval;
	ASSERT_EXTRACT_ONE_PARAM(IS_CLASSNAME_ERR, val, err_msg);

	if (likely(Z_TYPE_P(val) == IS_STRING)) {
		if (likely(zend_lookup_class(Z_STRVAL_P(val), Z_STRLEN_P(val), &ce TSRMLS_CC) == SUCCESS)) {
			// the class_exists_retval is true when the class isn't an interface
			// (not sure what exactly the second part of the bitwise flag logic ensures, the - operation obfuscates the flags)
			// this maintains exact parity with the default behavior of class_exists() and iface_exists()
			class_exists_retval = (((*ce)->ce_flags & (ZEND_ACC_INTERFACE | (ZEND_ACC_TRAIT - ZEND_ACC_EXPLICIT_ABSTRACT_CLASS))) == 0);
			iface_exists_retval = (((*ce)->ce_flags & ZEND_ACC_INTERFACE) > 0);

			if (likely(class_exists_retval || iface_exists_retval)) {
				RETURN_ZVAL(getThis(), 1, 0);
			}
		}
	}
	assert_throw_exception_on_unary_assertion(getThis(), IS_CLASSNAME_ERR, val, err_msg TSRMLS_CC);
	return;
}

PHP_METHOD(Assert, uses_trait)
{
	zval* obj;
	zval* trait_name;
	zval* err_msg = NULL;
	zend_class_entry **trait_ce_p;
	zend_class_entry *trait_ce;
	zend_class_entry *obj_ce;
	zend_class_entry *obj_trait_ce;
	zend_uint num_traits;
	ASSERT_EXTRACT_TWO_PARAMS(USES_TRAIT_ERR, obj, trait_name, err_msg);

	// BIG IMPORTANT NOTE: this function actually doesn't accurately judge whether a trait is used in a class because
	// this doesn't look at traits used by parent classes. This is equivalent to doing
	// array_key_exists($trait_name, class_uses($obj));
	if (likely(Z_TYPE_P(obj) == IS_OBJECT && Z_TYPE_P(trait_name) == IS_STRING)) { // verify types, then lookup trait
		if (likely(zend_lookup_class(Z_STRVAL_P(trait_name), Z_STRLEN_P(trait_name), &trait_ce_p TSRMLS_CC) == SUCCESS)) {

			trait_ce = *trait_ce_p;
			obj_ce = Z_OBJCE_P(obj);

			for (num_traits = 0; num_traits < obj_ce->num_traits; num_traits++) {
				obj_trait_ce = obj_ce->traits[num_traits];

				if (obj_trait_ce->name_length == trait_ce->name_length &&
				        !strncmp(obj_trait_ce->name, trait_ce->name, trait_ce->name_length)) {
					RETURN_ZVAL(getThis(), 1, 0);
				}
			}
		}
	}

	assert_throw_exception_on_binary_assertion(getThis(), USES_TRAIT_ERR, obj, trait_name, err_msg TSRMLS_CC);
	return;

}

/**
 * A quick note on the behavior of this function: This operates by ensuring that all of the keys
 * in the array are ascending, in order integers starting from 0. The function in php user-land
 * operates like this: if (array_keys($arr) !== array_keys(array_keys($arr)) throw exception;
 * These in fact do the same exact thing! Applying array_keys twice to $arr produces an array
 * where both the keys and values are increasing integers starting from 0. Applying array keys
 * to $arr once produces an array where the keys are in order ascending integers and the values
 * are the array keys. You can see how this works: the only way the two things are equal is if
 * the keys of $arr are in-order ascending integers as well!
 */
static int is_assoc_array_helper(zval* arr)
{
	ulong i = 0;
	ulong idx;
	int type;
	char *key;
	uint keylen;
	HashTable *array = Z_ARRVAL_P(arr);

	for (zend_hash_internal_pointer_reset(array);
	        zend_hash_has_more_elements(array) == SUCCESS;
	        zend_hash_move_forward(array)) {

		type = zend_hash_get_current_key_ex(array, &key, &keylen, &idx, 0, NULL);
		if (type == HASH_KEY_IS_STRING) {
			return 1;
		}
		if (idx != i) {
			return 1;
		}
		i++;
	}

	return 0;
}

PHP_METHOD(Assert, is_associative_array)
{
	zval *arr;
	zval *err_msg = NULL;
	ASSERT_EXTRACT_ONE_PARAM(IS_ASSOC_ARR_ERR, arr, err_msg);

	if (unlikely(Z_TYPE_P(arr) != IS_ARRAY)) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_ASSOC_ARR_ERR, arr, err_msg TSRMLS_CC);
		return;
	}
	if (unlikely(!is_assoc_array_helper(arr))) {
		assert_throw_exception_on_unary_assertion(getThis(), IS_ASSOC_ARR_ERR, arr, err_msg TSRMLS_CC);
		return;
	}
	RETURN_ZVAL(getThis(), 1, 0);
}

PHP_METHOD(Assert, is_in_array)
{
	zval *value,				/* value to check for */
	     *array,				/* array to check in */
	     **entry,				/* pointer to array entry */
	     res;					/* comparison result */
	HashPosition pos;			/* hash iterator */
	zval *err_msg = NULL;
	ASSERT_EXTRACT_TWO_PARAMS(IS_IN_ARRAY_ERR, array, value, err_msg);

	if (unlikely(Z_TYPE_P(array) != IS_ARRAY)) {
		assert_throw_exception_on_binary_assertion(getThis(), IS_IN_ARRAY_ERR, value, array, err_msg TSRMLS_CC);
		return;
	}

	zend_hash_internal_pointer_reset_ex(Z_ARRVAL_P(array), &pos);
	while (zend_hash_get_current_data_ex(Z_ARRVAL_P(array), (void **)&entry, &pos) == SUCCESS) {
		is_identical_function(&res, value, *entry TSRMLS_CC);

		// if a match was found, return!
		if (Z_LVAL(res)) {
			RETURN_ZVAL(getThis(), 1, 0);
		}
		// otherwise, advance the iterator
		zend_hash_move_forward_ex(Z_ARRVAL_P(array), &pos);
	}

	assert_throw_exception_on_binary_assertion(getThis(), IS_IN_ARRAY_ERR, value, array, err_msg TSRMLS_CC);
	return;
}

static void copy_global_assert_obj_to_return_value(zval *retval, zend_object_handle handle)
{
	// init the proper object value
	zend_object_value oval;
	oval.handlers = &fake_assert_obj_handlers;
	oval.handle = handle;

	// copy everything needed into return_value (a zval*)
	Z_TYPE_P(retval) = IS_OBJECT;
	Z_OBJVAL_P(retval) = oval;
	zval_copy_ctor(retval);
}

PHP_METHOD(Assert, argument)
{
	copy_global_assert_obj_to_return_value(return_value, AssertGlobals(invalid_arg_assert_handle));
	return;
}

PHP_METHOD(Assert, received_value)
{
	copy_global_assert_obj_to_return_value(return_value, AssertGlobals(unexpected_val_assert_handle));
	return;
}

PHP_METHOD(Assert, logic)
{
	copy_global_assert_obj_to_return_value(return_value, AssertGlobals(logic_exception_assert_handle));
	return;
}

PHP_METHOD(Assert, with)
{
	zval* classname;
	zend_class_entry **ce;
	char errbuf[128];
	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &classname) == FAILURE) {
		strncpy(errbuf, WITH_NO_ARG_ERR, 128);
		zend_throw_exception(spl_ce_InvalidArgumentException, errbuf, 0 TSRMLS_CC);
		return;
	}

	// make sure the classname is a valid classname!
	if (Z_TYPE_P(classname) != IS_STRING) {
		strncpy(errbuf, WITH_NO_STRING_ERR, 128);
		zend_throw_exception(spl_ce_InvalidArgumentException, errbuf, 0 TSRMLS_CC);
		return;
	}

	// classname has to be a subclass of Exception, otherwise throw exception!
	if (zend_lookup_class(Z_STRVAL_P(classname), Z_STRLEN_P(classname), &ce TSRMLS_CC) == FAILURE) {
		strncpy(errbuf, WITH_INVALID_CLASSNAME_ERR, 128);
		zend_throw_exception(spl_ce_InvalidArgumentException, errbuf, 0 TSRMLS_CC);
		return;
	}
	if (!instanceof_function(*ce, zend_exception_get_default(TSRMLS_C) TSRMLS_CC)) {
		strncpy(errbuf, WITH_NOT_EXCEPTION_SUBCLASS_ERR, 128);
		zend_throw_exception(spl_ce_InvalidArgumentException, errbuf, 0 TSRMLS_CC);
		return;
	}

	// we can now rest assured this assert object can be created - now make it!
	object_init_ex(return_value, fast_assert_ce);

	// now, finally, set the "exception_type" property on the new exception object
	zend_update_property(fast_assert_ce, return_value, "exception_type", sizeof("exception_type") - 1, classname TSRMLS_CC);

	return;
}

static zend_function_entry fast_assert_methods[] = {
	PHP_ME(Assert, __construct,  NULL, ZEND_ACC_PUBLIC | ZEND_ACC_CTOR)
	PHP_ME(Assert, is_true, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_truthy, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_empty, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_not_empty, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_false, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_integer, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_float, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_array, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_object, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_string, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_boolean, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_not_null, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_key_in_array, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_numeric, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_not_numeric, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_integery, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_scalar, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, are_same, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, are_not_same, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, are_equal, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, are_not_equal, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_callable, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_instance_of, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_classname, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, uses_trait, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_associative_array, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, is_in_array, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Assert, argument, NULL, ZEND_ACC_PUBLIC | ZEND_ACC_STATIC)
	PHP_ME(Assert, received_value, NULL, ZEND_ACC_PUBLIC | ZEND_ACC_STATIC)
	PHP_ME(Assert, logic, NULL, ZEND_ACC_PUBLIC | ZEND_ACC_STATIC)
	PHP_ME(Assert, with, NULL, ZEND_ACC_PUBLIC | ZEND_ACC_STATIC) {
		NULL, NULL, NULL
	}
};

/**
 * Stubs for all unneccesary methods for reserved Assert objects
 * (i.e. those returned by ::argument and ::logic, etc) in the object handler methods
 */
void stub_add_ref(zval *object TSRMLS_DC) {}
void stub_del_ref(zval *object TSRMLS_DC) {}
zend_object_value stub_clone_obj(zval *object TSRMLS_DC) {}

/*
 * Helper function to register a new assert object using a temporary zval
 * Returns the object handle (i.e. unsigned int) of the new object
 */
static zend_object_handle create_new_assert_object(TSRMLS_D)
{
	zval obj;
	Z_SET_ISREF_TO(obj, 0);
	Z_SET_REFCOUNT(obj, 0);
	object_init_ex(&obj, fast_assert_ce);
	return Z_OBJ_HANDLE(obj);
}

PHP_MINIT_FUNCTION(fast_assert)
{
	// register the fast assert class
	zend_class_entry ce;
	INIT_CLASS_ENTRY(ce, "Assert", fast_assert_methods);
	fast_assert_ce = zend_register_internal_class(&ce TSRMLS_CC);

	fake_assert_obj_handlers = *zend_get_std_object_handlers();
	fake_assert_obj_handlers.add_ref = stub_add_ref;
	fake_assert_obj_handlers.del_ref = stub_del_ref;
	fake_assert_obj_handlers.clone_obj = stub_clone_obj;

	/* init thread-safe fast assert global struct */
#ifdef ZTS
	ts_allocate_id(&fa_globals_id, sizeof(fast_assert_globals), NULL, NULL);
#endif

	return SUCCESS;
}

PHP_RINIT_FUNCTION(fast_assert)
{
	// register the 3 important assert objects!
	AssertGlobals(invalid_arg_assert_handle) = create_new_assert_object(TSRMLS_C);
	AssertGlobals(unexpected_val_assert_handle) = create_new_assert_object(TSRMLS_C);
	AssertGlobals(logic_exception_assert_handle) = create_new_assert_object(TSRMLS_C);
	return SUCCESS;
}

//// BEGIN BOILERPLATE //////

zend_module_entry fast_assert_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
	STANDARD_MODULE_HEADER,
#endif
	PHP_FAST_ASSERT_EXTNAME,
	NULL, /* functions */
	PHP_MINIT(fast_assert), /* minit */
	NULL, /* MSHUTDOWN */
	//NULL, /* RINIT */
	PHP_RINIT(fast_assert), /* RINIT */
	NULL, /* RSHUTDOWN */
	NULL, /* MINFO */
#if ZEND_MODULE_API_NO >= 20010901
	PHP_FAST_ASSERT_VERSION,
#endif
	STANDARD_MODULE_PROPERTIES
};

#ifdef COMPILE_DL_FAST_ASSERT
extern "C" {
	ZEND_GET_MODULE(fast_assert)
}
#endif

//// END BOILERPLATE //////
