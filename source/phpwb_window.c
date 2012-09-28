/*******************************************************************************

 WINBINDER - The native Windows binding for PHP for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 ZEND wrapper for window creation and manipulation functions

*******************************************************************************/

//----------------------------------------------------------------- DEPENDENCIES

#include "phpwb.h"

//----------------------------------------------------------- EXPORTED FUNCTIONS

ZEND_FUNCTION(wb_create_window)
{
	LONG pwboparent, pwbo;
    LONG wbclass, x = WBC_CENTER, y = WBC_CENTER, w = CW_USEDEFAULT, h = CW_USEDEFAULT, style = 0, lparam = 0;
    int nargs;
    zval *zcaption;
	char *caption = "";
	char *tooltip = "";

	TCHAR *wcsCaption = 0;
	TCHAR *wcsTooltip = 0;

	nargs = ZEND_NUM_ARGS();

    if(zend_parse_parameters(nargs TSRMLS_CC,
	 "ll|zllllll", &pwboparent, &wbclass, &zcaption, &x, &y, &w, &h, &style, &lparam) == FAILURE)
		return;

	if(pwboparent && !wbIsWBObj((void *)pwboparent, TRUE))
		RETURN_NULL()

	if(nargs == 5) {
		w = x;
		h = y;
		x = WBC_CENTER;
		y = WBC_CENTER;
	}

	switch(zcaption->type) {

		case IS_ARRAY:
			parse_array(zcaption, "ss", &caption, &tooltip);
			wcsCaption = Utf82WideChar(caption, strlen(caption));
			wcsTooltip = Utf82WideChar(tooltip, strlen(tooltip));
			break;

		case IS_STRING:
			wcsCaption = Utf82WideChar(zcaption->value.str.val, zcaption->value.str.len);
			break;

		case IS_NULL:
			*caption = '\0';
			break;
	}

	pwbo = (LONG)wbCreateWindow((PWBOBJ)pwboparent, wbclass, wcsCaption, wcsTooltip, x, y, w, h, 0, style, lparam);

	if(!pwbo) {
		zend_error(E_ERROR, "%s(): Error creating window",
		  get_active_function_name(TSRMLS_C));
		RETURN_NULL();
	}

	RETURN_LONG(pwbo);
}

ZEND_FUNCTION(wb_destroy_window)
{
	long pwbo;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "l", &pwbo) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()

	RETURN_BOOL(wbDestroyWindow((PWBOBJ)pwbo))
}

ZEND_FUNCTION(wb_get_instance)
{
	char *caption = "";
	int caption_len = 0;
	BOOL bringtofront = FALSE;

	TCHAR *szCaption = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "s|l", &caption, &caption_len, &bringtofront) == FAILURE)
		return;

	// This function could return the window handler instead of a BOOL,
	// but it wouldn't be useful. Windows wouldn't let the second PHP process to
	// use this handle directly to, say, change the window caption

    szCaption = Utf82WideChar(caption, caption_len);
	RETURN_BOOL(wbGetRequestedAppWindow(szCaption, bringtofront) != NULL);
}

/* Returns an array with the width and height of a control, window, image or image file,
  or an array with ListView column widths */

ZEND_FUNCTION(wb_get_size)
{
    zval *source;
	DWORD size;
	LONG lparam = 0;
	PWBOBJ pwbo;

	TCHAR *wcs = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "z|l", &source, &lparam) == FAILURE)
		return;

	if(!source)
		RETURN_NULL();

	if(source->type == IS_LONG) {	// It's an integer: PWBO, HBITMAP or HICON

		if(!source->value.lval)
			RETURN_NULL()
		else
			pwbo = (PWBOBJ)(void *)source->value.lval;

		if(wbIsWBObj(pwbo, FALSE)) {
			// lparam here means "column widths"
			if(pwbo->uClass == ListView && lparam) {
				int i, cols;
				int pwidths[MAX_LISTVIEWCOLS];

				// Build the array

				cols = wbGetListViewColumnWidths(pwbo, pwidths);
				array_init(return_value);
				for(i = 0; i < cols; i++)
					add_next_index_long(return_value, pwidths[i]);
				return;
			} else {
				// lparam here means "client area"
				size = wbGetWindowSize(pwbo, lparam);
			}

		} else {
			size = wbGetImageDimensions((HBITMAP)source->value.lval);
		}

	} else if(source->type == IS_STRING) {

		// Is source an image file?

		if(strchr(source->value.str.val, '.') && (strstr(source->value.str.val, ".bmp") || strstr(source->value.str.val, ".ico") || strstr(source->value.str.val, ".icl") ||
		  strstr(source->value.str.val, ".dll") || strstr(source->value.str.val, ".exe"))) {

			HBITMAP hbm;

			wcs = Utf82WideChar(source->value.str.val, source->value.str.len);
			hbm = wbLoadImage(wcs, 0, 0);
			if(hbm)
				//size = wbGetImageDimensions(wbLoadImage(source->value.str.val, 0, 0));
				size = wbGetImageDimensions(hbm);
		} else {

			SIZE siz;
			wcs = Utf82WideChar(source->value.str.val, source->value.str.len);
			if(wbGetTextSize(&siz, wcs, lparam))
				size = (DWORD)MAKELONG(siz.cx, siz.cy);
			else
				size = 0;
		}

	} else {
		zend_error(E_WARNING, "Invalid parameter type passed to function %s()",
		  get_active_function_name(TSRMLS_C));
		RETURN_NULL();
	}

	// Build the array

	switch(size) {
		case (DWORD)MAKELONG(WBC_CENTER, WBC_MINIMIZED):
			RETURN_LONG(WBC_MINIMIZED);
//		case (DWORD)MAKELONG(WBC_CENTER, WBC_MAXIMIZED):
//			RETURN_LONG(WBC_MAXIMIZED);
		case (DWORD)MAKELONG(WBC_CENTER, WBC_CENTER):
			RETURN_NULL();
	}

	array_init(return_value);
	add_next_index_long(return_value, LOWORD(size));
	add_next_index_long(return_value, HIWORD(size));
}

ZEND_FUNCTION(wb_set_size)
{
	long pwbo;
    long h = 65535;
    int nargs;
	zval *zparm = NULL;

	nargs = ZEND_NUM_ARGS();

    if(zend_parse_parameters(nargs TSRMLS_CC,
	 "lz|l", &pwbo, &zparm, &h) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()

	if((zparm->type == IS_ARRAY) && ((PWBOBJ)pwbo)->uClass == ListView) {

		int i, nelem;
		int pwidths[MAX_LISTVIEWCOLS];
		HashTable *target_hash;
		zval **entry;

		target_hash = HASH_OF(zparm);
		if(!target_hash)
			RETURN_NULL();

		nelem = zend_hash_num_elements(target_hash);
		zend_hash_internal_pointer_reset(target_hash);

		// Loop to read zparm items

		for(i = 0; i < nelem; i++) {
			if(zend_hash_get_current_data(target_hash, (void **) &entry) == FAILURE) {
				zend_error(E_WARNING, "%s(): Could not retrieve element %d from zparm",
				  get_active_function_name(TSRMLS_C), i);
				RETURN_NULL();
			}
			switch(Z_TYPE_PP(entry)) {

				case IS_LONG:
					pwidths[i] = (*entry)->value.lval;
					break;

				case IS_STRING:
				case IS_DOUBLE:
					convert_to_long_ex(entry);
					pwidths[i] = (*entry)->value.lval;
					break;

				case IS_NULL:
					convert_to_long_ex(entry);
					pwidths[i] = 65535;
					break;

				default:
					zend_error(E_WARNING, "Wrong data type in array in function %s()",
					  get_active_function_name(TSRMLS_C));
					RETURN_NULL();
			}

			zend_hash_move_forward(target_hash);
		}

		RETURN_BOOL(wbSetListViewColumnWidths((PWBOBJ)pwbo, pwidths));

	} else {

		if(zparm->type != IS_LONG) {
			zend_error(E_WARNING, "Wrong data type in width in function %s()",
			  get_active_function_name(TSRMLS_C));
			RETURN_NULL();
		}

		if(h != 65535)
			RETURN_BOOL(wbSetWindowSize((PWBOBJ)pwbo, zparm->value.lval, h, -1))
		else
			RETURN_BOOL(wbSetWindowSize((PWBOBJ)pwbo, 0, 0, zparm->value.lval))
	}
}

/* Returns an array with the x and y positions */

ZEND_FUNCTION(wb_get_position)
{
	long pwbo;
	DWORD pos;
	LONG clientarea = FALSE;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "l|l", &pwbo, &clientarea) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()

	// Build the array

	pos = wbGetWindowPosition((PWBOBJ)pwbo, NULL, clientarea);
	array_init(return_value);
	add_next_index_long(return_value, LOWORD(pos));
	add_next_index_long(return_value, HIWORD(pos));
}

ZEND_FUNCTION(wb_set_position)
{
	long pwbo, x, y;

	// Default parameter values

	x = WBC_CENTER;
	y = WBC_CENTER;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "l|ll", &pwbo, &x, &y) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()

	RETURN_BOOL(wbSetWindowPosition((PWBOBJ)pwbo, x, y, NULL));
}

ZEND_FUNCTION(wb_set_area)
{
    int nargs;
	long pwbo, type, x, y, w, h;

	// Default parameter values

	x = -1;
	y = -1;
	w = 0;
	h = 0;

	nargs = ZEND_NUM_ARGS();
    if(zend_parse_parameters(nargs TSRMLS_CC,
	 "ll|llll", &pwbo, &type, &x, &y, &w, &h) == FAILURE)
		return;

	// x, y, w, h must be supplied together

	switch(nargs) {
		case 3:
		case 4:
		case 5:
			zend_error(E_WARNING, "Invalid number of parameters passed to function %s()",
			  get_active_function_name(TSRMLS_C));
			RETURN_NULL();
	}

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()

	RETURN_BOOL(wbSetWindowArea((PWBOBJ)pwbo, type, x, y, w, h));
}

ZEND_FUNCTION(wb_set_handler)
{
	long pwbo;
	char *handler;
	char *objname;
	zval *name, *zparam;

	TCHAR *wcsObjname = 0;
	TCHAR *wcsHandler = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "lz", &pwbo, &zparam) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL();

	switch(zparam->type) {
		case IS_ARRAY:
			parse_array(zparam, "ss", &objname, &handler);
			break;
		case IS_STRING:
			handler = zparam->value.str.val;
			objname = NULL;
			break;
		default:
			zend_error(E_WARNING, "Wrong data type in function %s()",
			  get_active_function_name(TSRMLS_C));
			RETURN_NULL();
	}

	MAKE_STD_ZVAL(name);
	ZVAL_STRING(name, handler, 1);

	// Error checking is VERY POOR for user methods (i.e. when zparam is an array)

	if(!objname && !zend_is_callable(name, 0, &handler, TSRMLS_C)) {
		zend_error(E_WARNING, "%s(): '%s' is not a function or cannot be called",
		  get_active_function_name(TSRMLS_C), handler);
		efree(name);
		RETURN_NULL();
	} else {
		efree(name);

		wcsObjname = Utf82WideChar(objname, 0);
		wcsHandler = Utf82WideChar(handler, 0);
		RETURN_BOOL(wbSetWindowHandler((PWBOBJ)pwbo, wcsObjname, wcsHandler));
	}
}

ZEND_FUNCTION(wb_get_item_list)
{
	long pwboparent;
	PWBOBJ *plist;
	int nctrls, i;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "l", &pwboparent) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwboparent, TRUE))
		RETURN_NULL()

	// Build the array

	array_init(return_value);

	nctrls = wbGetControlList((PWBOBJ)pwboparent, NULL, 32767);
	if(nctrls) {
		plist = emalloc(nctrls * sizeof(PWBOBJ));
		wbGetControlList((PWBOBJ)pwboparent, plist, nctrls);
		for(i = 0; i < nctrls; i++)
			add_next_index_long(return_value, (LONG)plist[i]);
		efree(plist);
	}
}

//------------------------------------------------------------------ END OF FILE
