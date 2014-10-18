/*******************************************************************************

 WINBINDER - The native Windows binding for PHP for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 ZEND wrapper for Windows system functions

*******************************************************************************/

//----------------------------------------------------------------- DEPENDENCIES

#include "phpwb.h"
#include "php_ini.h"			// for cfg_get_string()

//-------------------------------------------------------------------- VARIABLES

// Private

static char szAppName[] = APPNAME;

// Public to wb_* modules

extern LPTSTR MakeWinPath(LPTSTR pszPath);

//----------------------------------------------------------- EXPORTED FUNCTIONS

// How can a function that is so important be so small?

ZEND_FUNCTION(wb_main_loop)
{
	RETURN_LONG(wbMainLoop());
}

ZEND_FUNCTION(wbtemp_set_accel_table)
{
	int i, nelem;
	zval *zarray, **entry;
	HashTable *target_hash;
	char *str_accel;
	ACCEL accel[MAX_ACCELS];
	DWORD dwacc;
	int naccel= 0;
	long pwbo;

	// Get function parameters

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "lz!", &pwbo, &zarray) == FAILURE)
		return;

	if(zarray->type == IS_ARRAY) {

		target_hash = HASH_OF(zarray);
		if(!target_hash)
			RETURN_NULL();

		nelem = zend_hash_num_elements(target_hash);
		zend_hash_internal_pointer_reset(target_hash);

		// Loop to read array items

		for(i = 0; i < nelem; i++) {
			if(zend_hash_get_current_data(target_hash, (void **) &entry) == FAILURE) {
				zend_error(E_WARNING, "Could not retrieve element %d from array in function %s()",
				  i, get_active_function_name(TSRMLS_C));
				RETURN_NULL();
			}

			switch(Z_TYPE_PP(entry)) {

				case IS_ARRAY:				// An accelerator item is an array inside an array
					parse_array(*entry, "ls", &accel[naccel].cmd, &str_accel);
					if(str_accel && *str_accel && naccel < MAX_ACCELS) {
						dwacc = wbMakeAccelFromString(str_accel);
						accel[naccel].key = LOWORD(dwacc);
						accel[naccel].fVirt = (BYTE)HIWORD(dwacc);
//						printf("> %d %d %d %s\n", accel[naccel].key, accel[naccel].fVirt, accel[naccel].cmd, str_accel);
						naccel++;
					}
					break;

				default:
					zend_error(E_WARNING, "%s(): Accelerator table must be an array of arrays with two elements",
					  get_active_function_name(TSRMLS_C));
					RETURN_NULL();
					break;
			}

			if(i < nelem - 1)
				zend_hash_move_forward(target_hash);
		}

		// Create accelerator table

		RETURN_BOOL(wbSetAccelerators((PWBOBJ)pwbo, accel, naccel));

	} else {
		RETURN_BOOL(wbSetAccelerators((PWBOBJ)pwbo, NULL, 0));
	}
}

ZEND_FUNCTION(wb_set_cursor)
{
	LONG pwbo;
	zval *source = NULL;
	HANDLE hCursor;
	LPTSTR pszCursorName;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "lz!", &pwbo, &source) == FAILURE)
		return;

	if(!source) {

		hCursor = NULL;
		pszCursorName = NULL;

	} else if(source->type == IS_LONG) {

		hCursor = (HANDLE)source->value.lval;
		pszCursorName = NULL;

	} else if(source->type == IS_STRING) {

		hCursor = NULL;
		pszCursorName = Utf82WideChar(source->value.str.val, source->value.str.len);

	} else {

		zend_error(E_WARNING, "Invalid parameter type passed to function %s()",
		  get_active_function_name(TSRMLS_C));
		RETURN_NULL();

	}

	RETURN_BOOL(wbSetCursor((PWBOBJ)pwbo, pszCursorName, hCursor))
}

/*
ZEND_FUNCTION(wb_load_media)
{
	int filename_len;
	LONG flags;
	char *filename;
    BOOL bRet;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "sl", &cmd, &filename_len, &flags) == FAILURE)
		return;

	if(!filename || !*filename)
		RETURN_NULL();

		PMEDIA pMd;

		MakeWinPath(source->value.str.val);
		pMd = wbOpenMedia(source->value.str.val, TRUE);
		if(!pMd) {
			zend_error(E_WARNING, "Could not open media file %s in function %s()",
			  source->value.str.val, get_active_function_name(TSRMLS_C));
			RETURN_NULL();
		}
		bRet = wbControlMedia(pMd, cmd, 0, 1);
		wbCloseMedia(pMd);
		RETURN_BOOL(bRet);
}

ZEND_FUNCTION(wb_destroy_media)
{
}
*/


ZEND_FUNCTION(wb_play_sound)
{
	int cmd_len;
	char *cmd = "";
    zval *source;

    TCHAR *szPath = 0;
    TCHAR *szCmd = 0;
    BOOL ret;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "z|s", &source, &cmd, &cmd_len) == FAILURE)
		return;

	if(!source)
		RETURN_NULL();

	if(source->type == IS_LONG) {			// It's an integer: Play system sound

		RETURN_BOOL(wbPlaySystemSound(source->value.lval));

	} else if(source->type == IS_STRING) {	// It's an empty string or filename

		if(*source->value.str.val) {
			szPath = Utf82WideChar(source->value.str.val, source->value.str.len);
			MakeWinPath(szPath);
			if(!EXISTFILE(szPath)) {
				wbFree(szPath);
				zend_error(E_WARNING, "Could not open media file %s in function %s()",
					source->value.str.val, get_active_function_name(TSRMLS_C));
				RETURN_BOOL(FALSE);
				return;
			}

			szCmd = Utf82WideChar(cmd, cmd_len);
		}

		ret = wbPlaySound(szPath, szCmd);

		if(ret) {
			RETURN_BOOL(TRUE);
		} else {
			zend_error(E_WARNING, "Unknown command '%s' in function %s()",
			  cmd, get_active_function_name(TSRMLS_C));
			RETURN_BOOL(FALSE);
		}

	} else {
		zend_error(E_WARNING, "Invalid parameter type passed to function %s()",
		  get_active_function_name(TSRMLS_C));
		RETURN_BOOL(FALSE);
	}
}

ZEND_FUNCTION(wb_stop_sound)
{
	int cmd_len;
	char *cmd = "";

	TCHAR *wcs = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "|s", &cmd, &cmd_len) == FAILURE)
		return;

    wcs = Utf82WideChar(cmd, cmd_len);
	RETURN_BOOL(wbStopSound(wcs));
}


ZEND_FUNCTION(wb_message_box)
{
	char *msg, *title = NULL;
	long pwbo, style = 0;
	int msg_len, title_len = 0, ret;

	TCHAR *szMsg = 0;
	TCHAR *szTitle = 0;

	style = MB_OK;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "ls|sl", &pwbo, &msg, &msg_len, &title, &title_len, &style) == FAILURE)
		return;

	if(pwbo && !wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()

	if(!title || !*title) {
		title = szAppName;
		title_len = strlen(szAppName);
	}

	szMsg = Utf82WideChar(msg, msg_len);
	szTitle = Utf82WideChar(title, title_len);
	ret = wbMessageBox((PWBOBJ)pwbo, szMsg, szTitle, style);

	switch(ret) {
		case -2:				// Error
			RETURN_NULL();
			break;
		case -1:				// IDNO
			RETURN_LONG(0);
			break;
		case 0:					// Cancel, etc.
			RETURN_BOOL(FALSE);
			break;
		case 1:					// OK, etc.
			RETURN_BOOL(TRUE);
			break;
	}
}

ZEND_FUNCTION(wb_exec)
{
	char *pgm, *parm = NULL;
	int pgm_len, parm_len = 0;
	BOOL show;

	TCHAR *szPgm = 0;
	TCHAR *szParm = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "s|sl", &pgm, &pgm_len, &parm, &parm_len, &show) == FAILURE)
		return;

    szPgm = Utf82WideChar(pgm, pgm_len);
    szParm = Utf82WideChar(parm, parm_len);
	RETURN_BOOL(wbExec(szPgm, szParm, show));
}

// TODO: without parameters, wb_get_system_info() should return an
// associative array with all information


ZEND_FUNCTION(wb_get_system_info)
{
	char *s;
	int s_len;
	BOOL isstr;
	LONG res;
	char strval[1024];

	TCHAR szVal[1024];
	TCHAR *wcs = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "s", &s, &s_len) == FAILURE)
		return;

	if(!stricmp(s, "extensionpath")) {

		// Calls Zend function cfg_get_string()

		char *value;
		TCHAR *szValue = 0;

		if(cfg_get_string("extension_dir", &value) == FAILURE)
			RETURN_BOOL(FALSE);
		szValue = Utf82WideChar(value, strlen(value));

		// Assemble the final string
		wbGetSystemInfo(TEXT("exepath"), &isstr, szVal, 1023);
		wcscat(szVal, szValue);
		MakeWinPath(szVal);
		wbFree(szValue);

		WideCharCopy(szVal, strval, 1024);
		isstr = TRUE;

	} else {
		wcs = Utf82WideChar(s, s_len);
		// Calls the API (low-level) WinBinder layer
		res = wbGetSystemInfo(wcs, &isstr, szVal, 1023);
		WideCharCopy(szVal, strval, 1024);
	}

	if(isstr) {
		if(!*strval && (res == -1)) {		// Unrecognized parameter
			RETURN_NULL();
		} else {
			RETURN_STRING(strval, strlen(strval));
		}
	} else {
		RETURN_LONG(res);
	}
}

/*

Search for a file in the Windows and System directories, in this order. If the file exists,
return the complete path to it. If not, return filename.

*/

ZEND_FUNCTION(wb_find_file)
{
	char *s;
	int s_len;
	char path[MAX_PATH * 4];

	TCHAR *szPath = 0;

	if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "s", &s, &s_len) == FAILURE)
		return;


	szPath = Utf82WideChar(s, s_len);
	wbFindFile(szPath, MAX_PATH * 4);

	WideCharCopy(szPath, path, MAX_PATH * 4);
	RETURN_STRING(path, TRUE);
}

/*

Reads a registry key as a string. If it is a DWORD, convert to a string

TODO: support binary data and other odd formats
NOTE: maximum string is 1024 characters

*/

ZEND_FUNCTION(wb_get_registry_key)
{
	char *key, *subkey, *entry = NULL;
	int key_len, subkey_len, entry_len;
	int buflen = 1024;
	char sval[1024];
	TCHAR szVal[1024];

	TCHAR *szKey = 0;
	TCHAR *szSubKey = 0;
	TCHAR *szEntry = 0;
	BOOL ret;

	if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "ss|s", &key, &key_len, &subkey, &subkey_len, &entry, &entry_len) == FAILURE)
		return;

	szKey = Utf82WideChar(key, key_len);
	szSubKey = Utf82WideChar(subkey, subkey_len);
	szEntry = Utf82WideChar(entry, entry_len);

	ret = wbReadRegistryKey(szKey, szSubKey, szEntry, szVal, &buflen);

	if(ret) {
		if(*szVal) {
			WideCharCopy(szVal, sval, buflen);
			RETURN_STRING(sval, TRUE)
		} else
			RETURN_STRING("", TRUE)
	} else {
		RETURN_NULL();
	}
}

ZEND_FUNCTION(wb_set_registry_key)
{
	char *key, *subkey, *entry;
	int key_len, subkey_len, entry_len;
    zval *source = NULL;

	TCHAR *szKey = 0;
	TCHAR *szSubKey = 0;
	TCHAR *szEntry = 0;
	TCHAR *szVal = 0;
	BOOL ret;

	if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "ss|sz!", &key, &key_len, &subkey, &subkey_len, &entry, &entry_len, &source) == FAILURE)
		return;

	if(!source) {
		szKey = Utf82WideChar(key, key_len);
		szSubKey = Utf82WideChar(subkey, subkey_len);
		szEntry = Utf82WideChar(entry, entry_len);

		ret = wbWriteRegistryKey(szKey, szSubKey, szEntry, NULL, 0, TRUE);

		RETURN_BOOL(ret);
	} else if(source->type == IS_LONG || source->type == IS_BOOL) {
		szKey = Utf82WideChar(key, key_len);
		szSubKey = Utf82WideChar(subkey, subkey_len);
		szEntry = Utf82WideChar(entry, entry_len);

		ret = wbWriteRegistryKey(szKey, szSubKey, szEntry, NULL, source->value.lval, FALSE);

		RETURN_BOOL(ret);
	} else if(source->type == IS_DOUBLE) {
		TCHAR szAux[50];
		wsprintf(szAux, TEXT("%20.20f"), source->value.dval);

		szKey = Utf82WideChar(key, key_len);
		szSubKey = Utf82WideChar(subkey, subkey_len);
		szEntry = Utf82WideChar(entry, entry_len);

		ret = wbWriteRegistryKey(szKey, szSubKey, szEntry, szAux, 0, TRUE);

		RETURN_BOOL(ret);
	} else if(source->type == IS_STRING) {
		szKey = Utf82WideChar(key, key_len);
		szSubKey = Utf82WideChar(subkey, subkey_len);
		szEntry = Utf82WideChar(entry, entry_len);
		szVal = Utf82WideChar(source->value.str.val, source->value.str.len);

		ret = wbWriteRegistryKey(szKey, szSubKey, szEntry, szVal, 0, TRUE);

		RETURN_BOOL(ret);
	} else {
		zend_error(E_WARNING, "Invalid parameter type passed to function %s()",
		  get_active_function_name(TSRMLS_C));
		RETURN_NULL();
	}
}

ZEND_FUNCTION(wb_create_timer)
{
	long pwbo;
	int id, ms;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "lll", &pwbo, &id, &ms) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL();

	RETURN_BOOL(wbSetTimer((PWBOBJ)pwbo, id, MAX(1, ms)));
}

ZEND_FUNCTION(wb_destroy_timer)
{
	long pwbo;
	int id;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "ll", &pwbo, &id) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL();

	RETURN_BOOL(wbSetTimer((PWBOBJ)pwbo, id, 0));
}

ZEND_FUNCTION(wb_wait)
{
	long pwbo = 0, pause = 0, flags = WBC_KEYDOWN;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "|lll", &pwbo, &pause, &flags) == FAILURE)
		return;

	if(pwbo != 0 && !wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL();

	RETURN_LONG(wbCheckInput((PWBOBJ)pwbo, flags, pause));
}

//------------------------------------------------------------------ END OF FILE
