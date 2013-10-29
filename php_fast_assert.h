#ifndef PHP_FAST_ASSERT_H
#define PHP_FAST_ASSERT_H

#define PHP_FAST_ASSERT_VERSION "0.1"
#define PHP_FAST_ASSERT_EXTNAME "fast_assert"

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

extern "C" {
#include "php.h"
}

typedef struct _fast_assert_globals {
	// global object handles pointing to the 3 commonly used assert objects
	zend_object_handle invalid_arg_assert_handle;
	zend_object_handle unexpected_val_assert_handle;
	zend_object_handle logic_exception_assert_handle;
} fast_assert_globals;

#ifdef ZTS
#define AssertGlobals(v) TSRMG(fa_globals_id, fast_assert_globals *, v)
extern int fa_globals_id;
#else
#define AssertGlobals(v) (fa_globals.v)
extern fast_assert_globals fa_globals;
#endif /* ZTS */

extern zend_module_entry fast_assert_module_entry;
#define phpext_fast_assert_ptr &fast_assert_module_entry;

#endif /* PHP_FAST_ASSERT_H */
