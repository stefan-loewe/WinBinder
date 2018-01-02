/*******************************************************************************

 WINBINDER - The native Windows binding for PHP for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 ZEND wrapper for menu functions

*******************************************************************************/

//----------------------------------------------------------------- DEPENDENCIES

#include "phpwb.h"

//----------------------------------------------------------- EXPORTED FUNCTIONS

// TODO: allocate accel[] dynamically like pitem

ZEND_FUNCTION(wbtemp_create_menu)
{
	int i, nelem;
	zval *zarray = NULL, *entry = NULL;
	HashTable *target_hash;
	zend_long pwboParent;
	PWBITEM *pitem = NULL;
	LONG l;
	char *str_accel = NULL;
	ACCEL accel[MAX_ACCELS];
	DWORD dwacc;
	int naccel= 0;

	// Get function parameters

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "lz!", &pwboParent, &zarray) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwboParent, TRUE))
		RETURN_NULL()

	if(Z_TYPE_P(zarray) == IS_ARRAY) {

		target_hash = HASH_OF(zarray);
		if(!target_hash)
			RETURN_NULL();

		nelem = zend_hash_num_elements(target_hash);
		zend_hash_internal_pointer_reset(target_hash);

		// Allocate memory for menu item pointers

		pitem = emalloc(nelem * sizeof(PWBITEM));

		// Loop to read array items

		for(i = 0; i < nelem; i++) {
			if((entry = zend_hash_get_current_data(target_hash)) == NULL) {
				zend_error(E_WARNING, "Could not retrieve element %d from array in function %s()",
				  i, get_active_function_name(TSRMLS_C));
				efree(pitem);
				RETURN_NULL();
			}

			// Allocate memory for menu item description

			pitem[i] = emalloc(sizeof(WBITEM));

			switch(Z_TYPE_P(entry)) {

				case IS_ARRAY:				// A menu item is an array inside an array
					parse_array(entry, "lssss", &pitem[i]->id, &pitem[i]->pszCaption, &pitem[i]->pszHint, &pitem[i]->pszImage, &str_accel);
					pitem[i]->pszCaption = Utf82WideChar(pitem[i]->pszCaption, 0);
					pitem[i]->pszHint = Utf82WideChar(pitem[i]->pszHint, 0);
					pitem[i]->pszImage = Utf82WideChar(pitem[i]->pszImage, 0);


					if(str_accel && *str_accel && naccel < MAX_ACCELS) {
						dwacc = wbMakeAccelFromString(str_accel);
						accel[naccel].key = LOWORD(dwacc);
						accel[naccel].fVirt = (BYTE)HIWORD(dwacc);
						accel[naccel].cmd = pitem[i]->id;
	//					printf(">>> %d %d %d\n", accel[naccel].key, accel[naccel].fVirt, accel[naccel].cmd);
						naccel++;
					}
					break;

				case IS_NULL:				// Separator
					pitem[i] = NULL;
					break;

				case IS_STRING:				// Create first-level menu
					pitem[i]->id = 0;
					pitem[i]->index = 0;
					pitem[i]->pszCaption = Utf82WideChar(Z_STRVAL_P(entry), Z_STRLEN_P(entry));
					pitem[i]->pszHint = NULL;
					pitem[i]->pszImage = NULL;
					break;
			}

			if(i < nelem - 1)
				zend_hash_move_forward(target_hash);
		}

		// Create accelerator table

		wbSetAccelerators((PWBOBJ)pwboParent, accel, naccel);

	} else {
		nelem = 0;
		pitem = NULL;
	}

	// Create menu and attach it to window

	l = (LONG)wbCreateMenu((PWBOBJ)pwboParent, pitem, nelem);

	if(pitem)
		efree(pitem);
	RETURN_LONG(l);
}

ZEND_FUNCTION(wbtemp_get_menu_item_checked)
{
	zend_long id;
	zend_long pwbo;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "ll", &pwbo, &id) == FAILURE)
		return;

	((PWBOBJ)pwbo)->id = id;

	RETURN_BOOL(wbGetMenuItemChecked((PWBOBJ)pwbo));
}

ZEND_FUNCTION(wbtemp_set_menu_item_checked)
{
	zend_long id, b;
	zend_long pwbo;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "lll", &pwbo, &id, &b) == FAILURE)
		return;

	((PWBOBJ)pwbo)->id = id;

	RETURN_BOOL(wbSetMenuItemChecked((PWBOBJ)pwbo, b));
}

/*
	Insert a bullet
	NOTE: state is not used, must be 1
*/

ZEND_FUNCTION(wbtemp_set_menu_item_selected)
{
	zend_long pwbo, id, state;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "lll", &pwbo, &id, &state) == FAILURE)
		return;

	((PWBOBJ)pwbo)->id = id;

	RETURN_BOOL(wbSetMenuItemSelected((PWBOBJ)pwbo))
}

ZEND_FUNCTION(wbtemp_set_menu_item_image)
{
	zend_long id, handle;
	zend_long pwbo;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "lll", &pwbo, &id, &handle) == FAILURE)
		return;

	((PWBOBJ)pwbo)->id = id;

	RETURN_BOOL(wbSetMenuItemImage((PWBOBJ)pwbo, (HANDLE)handle));
}

//------------------------------------------------------------------ END OF FILE
