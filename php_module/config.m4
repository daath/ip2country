dnl $Id: config.m4,v 1.1 2004/05/05 10:45:27 lars Exp $
dnl config.m4 for extension ip2c

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

dnl PHP_ARG_WITH(ip2c, for ip2c support,
dnl Make sure that the comment is aligned:
dnl [  --with-ip2c             Include ip2c support])

dnl Otherwise use enable:

PHP_ARG_ENABLE(ip2c, whether to enable ip2c support,
[  --enable-ip2c           Enable ip2c support])

if test "$PHP_IP2C" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-ip2c -> check with-path
  dnl SEARCH_PATH="/usr/local /usr"     # you might want to change this
  dnl SEARCH_FOR="/include/ip2c.h"  # you most likely want to change this
  dnl if test -r $PHP_IP2C/; then # path given as parameter
  dnl   IP2C_DIR=$PHP_IP2C
  dnl else # search default path list
  dnl   AC_MSG_CHECKING([for ip2c files in default path])
  dnl   for i in $SEARCH_PATH ; do
  dnl     if test -r $i/$SEARCH_FOR; then
  dnl       IP2C_DIR=$i
  dnl       AC_MSG_RESULT(found in $i)
  dnl     fi
  dnl   done
  dnl fi
  dnl
  dnl if test -z "$IP2C_DIR"; then
  dnl   AC_MSG_RESULT([not found])
  dnl   AC_MSG_ERROR([Please reinstall the ip2c distribution])
  dnl fi

  dnl # --with-ip2c -> add include path
  dnl PHP_ADD_INCLUDE($IP2C_DIR/include)

  dnl # --with-ip2c -> check for lib and symbol presence
  dnl LIBNAME=ip2c # you may want to change this
  dnl LIBSYMBOL=ip2c # you most likely want to change this 

  dnl PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  dnl [
  dnl   PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $IP2C_DIR/lib, IP2C_SHARED_LIBADD)
  dnl   AC_DEFINE(HAVE_IP2CLIB,1,[ ])
  dnl ],[
  dnl   AC_MSG_ERROR([wrong ip2c lib version or lib not found])
  dnl ],[
  dnl   -L$IP2C_DIR/lib -lm -ldl
  dnl ])
  dnl
  dnl PHP_SUBST(IP2C_SHARED_LIBADD)

  PHP_NEW_EXTENSION(ip2c, ip2c.c ip2country.c, $ext_shared)
fi
