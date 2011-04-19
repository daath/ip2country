/*
  +----------------------------------------------------------------------+
  | PHP Version 4														|
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2003 The PHP Group								|
  +----------------------------------------------------------------------+
  | This source file is subject to version 2.02 of the PHP license,	  |
  | that is bundled with this package in the file LICENSE, and is		|
  | available at through the world-wide-web at						   |
  | http://www.php.net/license/2_02.txt.								 |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to		  |
  | license@php.net so we can mail you a copy immediately.			   |
  +----------------------------------------------------------------------+
  | Author:	L. Petersen, Weird Silence, www.weirdsilence.net			  |
  +----------------------------------------------------------------------+

  $Id: ip2c.c,v 1.4 2004/05/09 15:55:47 lars Exp $ 
*/

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_ip2c.h"

#include "ip2country.h"

/* If you declare any globals in php_ip2c.h uncomment this:
ZEND_DECLARE_MODULE_GLOBALS(ip2c)
*/

/* True global resources - no need for thread safety here */
static int le_ip2c;

/* {{{ ip2c_functions[]
 *
 * Every user visible function must have an entry in ip2c_functions[].
 */
function_entry ip2c_functions[] = {
	ZEND_FE(ip2c_init, NULL)
	ZEND_FE(ip2c_lookup, NULL)
	ZEND_FE(ip2c_countrycode, NULL)
	ZEND_FE(ip2c_destroy, NULL)
	{NULL, NULL, NULL}	/* Must be the last line in ip2c_functions[] */
};
/* }}} */

/* {{{ ip2c_module_entry
 */
zend_module_entry ip2c_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
	STANDARD_MODULE_HEADER,
#endif
	"ip2c",
	ip2c_functions,
	ZEND_MINIT(ip2c),
	ZEND_MSHUTDOWN(ip2c),
	NULL,		/* Replace with NULL if there's nothing to do at request start */
	NULL,	/* Replace with NULL if there's nothing to do at request end */
	ZEND_MINFO(ip2c),
#if ZEND_MODULE_API_NO >= 20010901
	"0.9", /* Replace with version number for your extension */
#endif
	STANDARD_MODULE_PROPERTIES
};
/* }}} */

#ifdef COMPILE_DL_IP2C
ZEND_GET_MODULE(ip2c)
#endif

ZEND_MINIT_FUNCTION(ip2c)
{
	REGISTER_LONG_CONSTANT("IP2C_IP_NOT_FOUND", IP2C_IP_NOT_FOUND, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("IP2C_NOT_IP2C_DATABASE", IP2C_NOT_IP2C_DATABASE, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("IP2C_DATABASE_OPEN_ERROR", IP2C_DATABASE_OPEN_ERROR, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("IP2C_CALL_INIT_FIRST", IP2C_CALL_INIT_FIRST, CONST_CS | CONST_PERSISTENT);
	return SUCCESS;
}

ZEND_MSHUTDOWN_FUNCTION(ip2c)
{
	ip2c_destroy();
	return SUCCESS;
}

ZEND_MINFO_FUNCTION(ip2c)
{
	php_info_print_table_start();
	php_info_print_table_row(2, "IP 2 Country Lookup", "enabled");
	php_info_print_table_row(2, "Version", "0.9");
	php_info_print_table_end();
}

ZEND_FUNCTION(ip2c_init)
{
	int result = 0;
	char* filename;
	int filename_len;	

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &filename, &filename_len) == FAILURE) {
		return;
	}
	// Call the the real one here

	result = ip2c_init( filename );
	
	RETURN_LONG(result);
}

ZEND_FUNCTION(ip2c_lookup)
{
	char* ipnum;
	int ipnum_len;	
	short countryindex;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &ipnum, &ipnum_len) == FAILURE) {
		return;
	}

	// Call the the real one here
	countryindex = ip2c_lookup( ipnum );
	
	RETURN_LONG(countryindex);
}

ZEND_FUNCTION(ip2c_countrycode)
{
	int countryindex;
	char* country;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "l", &countryindex) == FAILURE) {
		return;
	}
	// Call the the real one here
	
	country = ip2c_idx2country( countryindex );
	
	RETURN_STRING(country, 1);
}

ZEND_FUNCTION(ip2c_destroy)
{
	// Call the the real one here

	ip2c_destroy();
	
	RETVAL_NULL();
}

