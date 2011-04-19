/*
  +----------------------------------------------------------------------+
  | PHP Version 4                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2003 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 2.02 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available at through the world-wide-web at                           |
  | http://www.php.net/license/2_02.txt.                                 |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author:	L. Petersen, Weird Silence, www.weirdsilence.net			  |  
  +----------------------------------------------------------------------+

  $Id: php_ip2c.h,v 1.3 2004/05/09 15:55:47 lars Exp $ 
*/

#ifndef PHP_IP2C_H
#define PHP_IP2C_H

extern zend_module_entry ip2c_module_entry;
#define phpext_ip2c_ptr &ip2c_module_entry

#ifdef PHP_WIN32
#define PHP_IP2C_API __declspec(dllexport)
#else
#define PHP_IP2C_API
#endif

#ifdef ZTS
#include "TSRM.h"
#endif

ZEND_MINIT_FUNCTION(ip2c);
ZEND_MSHUTDOWN_FUNCTION(ip2c);
ZEND_MINFO_FUNCTION(ip2c);

ZEND_FUNCTION(ip2c_init);
ZEND_FUNCTION(ip2c_lookup);
ZEND_FUNCTION(ip2c_countrycode);
ZEND_FUNCTION(ip2c_destroy);

/* 
  	Declare any global variables you may need between the BEGIN
	and END macros here:     

ZEND_BEGIN_MODULE_GLOBALS(ip2c)
	long  global_value;
	char *global_string;
ZEND_END_MODULE_GLOBALS(ip2c)
*/

/* In every utility function you add that needs to use variables 
   in php_ip2c_globals, call TSRMLS_FETCH(); after declaring other 
   variables used by that function, or better yet, pass in TSRMLS_CC
   after the last function argument and declare your utility function
   with TSRMLS_DC after the last declared argument.  Always refer to
   the globals in your function as IP2C_G(variable).  You are 
   encouraged to rename these macros something shorter, see
   examples in any other php module directory.
*/

#ifdef ZTS
#define IP2C_G(v) TSRMG(ip2c_globals_id, zend_ip2c_globals *, v)
#else
#define IP2C_G(v) (ip2c_globals.v)
#endif

#endif	/* PHP_IP2C_H */


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * indent-tabs-mode: t
 * End:
 */
