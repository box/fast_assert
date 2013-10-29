PHP_ARG_ENABLE(fast_assert, whether to enable fast_assert support,
[  --enable-fast_assert           Enable fast_assert support])

if test "$PHP_FAST_ASSERT" != "no"; then
  PHP_REQUIRE_CXX()
  PHP_SUBST(FAST_ASSERT_SHARED_LIBADD)
  PHP_ADD_LIBRARY(stdc++, 1, FAST_ASSERT_SHARED_LIBADD)
  AC_DEFINE(HAVE_FAST_ASSERT, 1, [Whether you have Fast Assert])
  PHP_NEW_EXTENSION(fast_assert, fast_assert.cc, $ext_shared)
fi