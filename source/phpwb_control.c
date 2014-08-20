/*******************************************************************************

 WINBINDER - The native Windows binding for PHP for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 ZEND wrapper for window controls

*******************************************************************************/

//----------------------------------------------------------------- DEPENDENCIES

#include "phpwb.h"

//----------------------------------------------------------- PRIVATE PROTOTYPES

// External

extern BOOL DisplayHTMLPage(PWBOBJ pwbo, LPCTSTR pszWebPageName);

//----------------------------------------------------------- EXPORTED FUNCTIONS

/* Creates a window control, menu, toolbar, status bar or accelerator. */

ZEND_FUNCTION(wbtemp_create_control)
{
	LONG pwboparent;
    LONG wbclass, x = WBC_CENTER, y = WBC_CENTER;
    LONG w = CW_USEDEFAULT, h = CW_USEDEFAULT, id = 32767, style = 0, param = 0, ntab = 0;
	int nargs;
    zval *zcaption;
	char *caption = "";
	char *tooltip = "";

	TCHAR *wcsCaption = 0;
	TCHAR *wcsTooltip = 0;

	nargs = ZEND_NUM_ARGS();

	if(zend_parse_parameters(nargs TSRMLS_CC,
	 "ll|zllllllll", &pwboparent, &wbclass, &zcaption, &x, &y, &w, &h, &id, &style, &param, &ntab) == FAILURE)
		return;

	if(nargs == 5) {
		w = x;
		h = y;
		x = WBC_CENTER;
		y = WBC_CENTER;
	}

	if(!wbIsWBObj((void *)pwboparent, TRUE))
		RETURN_NULL()

	switch(zcaption->type) {

		case IS_ARRAY:
			parse_array(zcaption, "ss", &caption, &tooltip);
			wcsCaption = Utf82WideChar(caption, 0);
			wcsTooltip = Utf82WideChar(tooltip, 0);
			break;

		case IS_STRING:
			wcsCaption = Utf82WideChar(zcaption->value.str.val, zcaption->value.str.len);
			break;

		case IS_NULL:
			// 2011_11_24 - Stefan Loewe: line below commented out, because if it fails with Windows 7 32 bit, with PHP 5.4 RC1
			//*caption = '\0';
			break;
	}

	// Convert line breaks for the caption and tooltip
	RETURN_LONG((LONG)wbCreateControl((PWBOBJ)pwboparent, wbclass, wcsCaption, wcsTooltip, x, y, w, h, id, style, param, ntab));
}

ZEND_FUNCTION(wb_destroy_control)
{
	LONG pwbo;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "l", &pwbo) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()
	else
		RETURN_BOOL(wbDestroyControl((PWBOBJ)pwbo));
}

ZEND_FUNCTION(wb_get_visible)
{
	LONG pwbo;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "l", &pwbo) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()
	else
		RETURN_BOOL(wbGetVisible((PWBOBJ)pwbo));
}

ZEND_FUNCTION(wb_set_visible)
{
	LONG pwbo;
    long b;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "ll", &pwbo, &b) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()
	else
		RETURN_BOOL(wbSetVisible((PWBOBJ)pwbo, b));
}

ZEND_FUNCTION(wb_set_focus)
{
	LONG pwbo;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "l", &pwbo) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()
	else
		RETURN_BOOL(wbSetFocus((PWBOBJ)pwbo))
}

/* Sets the state of a control item */

ZEND_FUNCTION(wb_set_state)
{
	LONG pwbo, item;
	BOOL state = TRUE;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "lll", &pwbo, &item, &state) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()

	if(((PWBOBJ)pwbo)->uClass == TreeView) {	// Set expanded / collapsed state
		RETURN_BOOL(wbSetTreeViewItemState((PWBOBJ)pwbo, (HTREEITEM)item, state));
	} else
		RETURN_NULL();
}

/* Gets the state of a control item */

ZEND_FUNCTION(wb_get_state)
{
	LONG pwbo, item;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "ll", &pwbo, &item) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()

	if(((PWBOBJ)pwbo)->uClass == TreeView) {	// Get expanded / collapsed state
		RETURN_BOOL(wbGetTreeViewItemState((PWBOBJ)pwbo, (HTREEITEM)item));
	} else
		RETURN_NULL();
}

/* Gets the parent of a control or control item */

ZEND_FUNCTION(wb_get_parent)
{
	LONG pwbo, item = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "l|l", &pwbo, &item) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()

	if(((PWBOBJ)pwbo)->uClass == TreeView) {
		if(item) {
			RETURN_LONG((LONG)wbGetTreeViewItemParent((PWBOBJ)pwbo, (HTREEITEM)item));
		} else {
			RETURN_LONG((LONG)((PWBOBJ)pwbo)->parent);
		}
	} else {
		RETURN_LONG((LONG)((PWBOBJ)pwbo)->parent);
	}
}


ZEND_FUNCTION(wb_get_focus)
{
	RETURN_LONG((LONG)wbGetFocus())
}

ZEND_FUNCTION(wb_set_style)
{
	LONG pwbo, style;
	BOOL value = TRUE;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "ll|l", &pwbo, &style, &value) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL();

	RETURN_BOOL(wbSetStyle((PWBOBJ)pwbo, style, value));
}

ZEND_FUNCTION(wb_get_class)
{
	LONG pwbo;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "l", &pwbo) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL();

	RETURN_LONG(((PWBOBJ)pwbo)->uClass);
}

ZEND_FUNCTION(wb_set_range)
{
	LONG pwbo, min = 0, max = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "lll", &pwbo, &min, &max) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL();

	RETURN_BOOL(wbSetRange((PWBOBJ)pwbo, min, max));
}

ZEND_FUNCTION(wb_get_id)
{
	LONG pwbo;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "l", &pwbo) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL();

	RETURN_LONG(((PWBOBJ)pwbo)->id);
}

ZEND_FUNCTION(wb_get_value)
{
	LONG pwbo, item = -1, subitem = -1;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "l|ll", &pwbo, &item, &subitem) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL();

	((PWBOBJ)pwbo)->item = item;
	((PWBOBJ)pwbo)->subitem = subitem;

	if(((PWBOBJ)pwbo)->uClass == ListView) {		// ListView: returns checked items

		int i, nitems;
		int *items;

		// How many items are checked?

		nitems = wbGetListViewCheckedItems((PWBOBJ)pwbo, NULL);
		if(!nitems)				// No items checked
			RETURN_NULL();

		// Call the function again, this time to fill up the item array

		items = emalloc(sizeof(int) * nitems);
		wbGetListViewCheckedItems((PWBOBJ)pwbo, items);

		// Build the return array

		array_init(return_value);
		for(i = 0; i < nitems; i++)
			add_next_index_long(return_value, items[i]);

		efree(items);

	} else if(((PWBOBJ)pwbo)->uClass == TreeView) {	// TreeView: returns whatever zval is

		zval *zparam;

		zparam = (zval *)wbGetValue((PWBOBJ)pwbo);
		if(!zparam)
			RETURN_NULL();

		switch(zparam->type) {
			case IS_LONG:
			case IS_BOOL:
				RETURN_LONG(zparam->value.lval);
				break;
			case IS_STRING:
				RETURN_STRINGL(zparam->value.str.val, zparam->value.str.len, TRUE);
				break;
			case IS_DOUBLE:
				RETURN_DOUBLE(zparam->value.dval);
				break;
			default:
				RETURN_NULL();
		}

	} else {

		RETURN_LONG(wbGetValue((PWBOBJ)pwbo));

	}
}

ZEND_FUNCTION(wb_get_selected)
{
	LONG pwbo;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "l", &pwbo) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL();

	if(((PWBOBJ)pwbo)->uClass == TabControl) {				// TabControl

		RETURN_LONG(TabCtrl_GetCurSel(((PWBOBJ)pwbo)->hwnd));

	} else if(((PWBOBJ)pwbo)->uClass == ListView) {			// ListView: returns selected items

		int i, nitems;
		int *items;

		// How many items are selected?

		nitems = wbGetListViewSelectedItems((PWBOBJ)pwbo, NULL);
		if(!nitems)				// No items selected
			RETURN_NULL();

		// Call the function again, this time to fill up the item array

		items = emalloc(sizeof(int) * nitems);
		wbGetListViewSelectedItems((PWBOBJ)pwbo, items);

		// Build the return array

		array_init(return_value);
		for(i = 0; i < nitems; i++)
			add_next_index_long(return_value, items[i]);

		efree(items);

	} else if(((PWBOBJ)pwbo)->uClass == TreeView) {			// TreeView: returns the handle of the selected node

		RETURN_LONG((LONG)wbGetTreeViewItemSelected((PWBOBJ)pwbo));

	} else

		RETURN_LONG(wbGetSelected((PWBOBJ)pwbo));
}


/*
  bool wb_set_image (int wbobject, mixed source [, int transparentcolor [, int index [, int param]]])
*/

ZEND_FUNCTION(wb_set_image)
{
    LONG pwbo, trcolor = NOCOLOR, index = 0, param = 0;
    zval *source = NULL;
	HANDLE hImage;
	TCHAR *wcs = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "lz!|lll", &pwbo, &source, &trcolor, &index, &param) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL();

	// Get the image handle from source

	if(!source) {

		hImage = NULL;

	} else if(source->type == IS_LONG) {

		hImage = (HANDLE)source->value.lval;

	} else if(source->type == IS_STRING) {

		// Here param is the icon size: set it to 1 for a small icon
		wcs = Utf82WideChar(source->value.str.val, source->value.str.len);
		hImage = wbLoadImage(wcs, MAX(0, index), param);
		wbFree(wcs);

		if(!hImage) {
			zend_error(E_WARNING, "Invalid image file %s or image index %d",
				source->value.str.val, index);
			RETURN_NULL();
		}

	} else {

		zend_error(E_WARNING, "Invalid parameter type passed to function %s()",
		  get_active_function_name(TSRMLS_C));
		RETURN_NULL();

	}

//	printf("%d %d %d %d\n", pwbo, trcolor, param, hImage);

	// Here param is the number of images for the ImageList

	RETURN_BOOL(wbSetImage((PWBOBJ)pwbo, hImage, trcolor, param));
}


/*
	Selects an icon from an imagelist and sets a control item/subitem to it

	bool wb_set_item_image (int wbobject, mixed index [, int item [, int subitem]])
*/

ZEND_FUNCTION(wb_set_item_image)
{
    LONG pwbo, item = 0, subitem = 0;
    zval *zindex = NULL;
    int nclass, index1 = 0, index2 = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "lz|ll", &pwbo, &zindex, &item, &subitem) == FAILURE)
		return;

	nclass = ((PWBOBJ)pwbo)->uClass;

	switch(nclass) {

		case ListView:

			index1 = zindex->value.lval;
			if(zindex->type != IS_NULL && zindex->type != IS_LONG) {
				zend_error(E_WARNING, "Parameter 2 expected to be an integer or NULL in function %s()",
					get_active_function_name(TSRMLS_C));
				RETURN_BOOL(FALSE);
			} else if(zindex->type == IS_NULL || (zindex->type == IS_LONG && zindex->value.lval < 0)) {
				RETURN_BOOL(wbSetListViewItemImage((PWBOBJ)pwbo, item, subitem, -1));
			} else {
				RETURN_BOOL(wbSetListViewItemImage((PWBOBJ)pwbo, item, subitem, index1));
			}
			break;

		case TreeView:

			if(zindex->type == IS_ARRAY)
				parse_array(zindex, "ll", &index1, &index2);
			else
				index1 = zindex->value.lval;

			RETURN_BOOL(wbSetTreeViewItemImages((PWBOBJ)pwbo, (HTREEITEM)item, index1, index2));
			break;

		case ListBox:
			// Future implementation goes here
		case ComboBox:
			// Future implementation goes here
		default:
			zend_error(E_WARNING, "Function %s() is not implemented for class #%d",
				get_active_function_name(TSRMLS_C), nclass);
	}
}


ZEND_FUNCTION(wb_get_control)
{
    LONG id = 0;
	LONG pwboparent;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "l|l", &pwboparent, &id) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwboparent, TRUE))
		RETURN_NULL()
	else
		RETURN_LONG((LONG)wbGetControl((PWBOBJ)pwboparent, id));
}

ZEND_FUNCTION(wb_set_enabled)
{
	LONG pwbo, state;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "ll", &pwbo, &state) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()
	else
		RETURN_BOOL(wbSetEnabled((PWBOBJ)pwbo, state));
}

ZEND_FUNCTION(wb_get_enabled)
{
	LONG pwbo;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "l", &pwbo) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()
	else
		RETURN_BOOL(wbGetEnabled((PWBOBJ)pwbo));
}

/* bool wb_refresh (int control [, bool now]) */

ZEND_FUNCTION(wb_refresh)
{
	LONG pwbo, now = TRUE;
	LONG x = 0, y = 0, width = 0, height = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "l|lllll", &pwbo, &now, &x, &y, &width, &height) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()
	else
		RETURN_BOOL(wbRefreshControl((PWBOBJ)pwbo, x, y, width, height, now));
}

ZEND_FUNCTION(wb_get_item_count)
{
	LONG pwbo;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "l", &pwbo) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()
	else
		RETURN_LONG(wbGetItemCount((PWBOBJ)pwbo));
}

ZEND_FUNCTION(wb_delete_items)
{
	LONG pwbo;
	zval *zitems = NULL;
	BOOL bRet = TRUE;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "l|z!", &pwbo, &zitems) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()

	if(!zitems)						// Delete all items
		RETURN_LONG(wbDeleteItems((PWBOBJ)pwbo, TRUE));

	switch(zitems->type) {

		case IS_NULL:				// Delete all items
			RETURN_LONG(wbDeleteItems((PWBOBJ)pwbo, TRUE));
			break;

		case IS_LONG:				// Delete an item
		case IS_BOOL:
			((PWBOBJ)pwbo)->item = zitems->value.lval;
			RETURN_LONG(wbDeleteItems((PWBOBJ)pwbo, FALSE));
			break;

		case IS_ARRAY:
			{
				zval *zitem;

				while((zitem = process_array(zitems, TSRMLS_C)) != NULL) {
					((PWBOBJ)pwbo)->item = zitem->value.lval;
					if(!wbDeleteItems((PWBOBJ)pwbo, FALSE))
						bRet = FALSE;
				}
			}

			RETURN_BOOL(bRet);

		case IS_STRING:				// Invalid parameter
		case IS_DOUBLE:
		default:
			zend_error(E_WARNING, "Parameter 2 expected to be an integer or array in function %s()",
				get_active_function_name(TSRMLS_C));
			RETURN_NULL();
	}
}

ZEND_FUNCTION(wb_sort)
{
	LONG pwbo, ascending = TRUE, subitem = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "l|ll", &pwbo, &ascending, &subitem) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()
	else {
		if(((PWBOBJ)pwbo)->uClass == ListView)
			RETURN_BOOL(wbSortLVColumn((PWBOBJ)pwbo, subitem, ascending))
		else
			RETURN_BOOL(FALSE);
	}
}

ZEND_FUNCTION(wb_set_location)
{
	char *location;
	int location_len;
	LONG pwbo;

	TCHAR *wcs = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "ls", &pwbo, &location, &location_len) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()

	if(((PWBOBJ)pwbo)->uClass == HTMLControl) {
		wcs = Utf82WideChar(location, location_len);
		RETURN_BOOL(DisplayHTMLPage((PWBOBJ)pwbo, wcs));
	}
	else
		RETURN_NULL();
}

//------------------------------------------------- AUXILIARY EXPORTED FUNCTIONS

ZEND_FUNCTION(wbtemp_select_tab)
{
	LONG pwbo;
	int ntab;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "ll", &pwbo, &ntab) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL();

	RETURN_BOOL(wbSelectTab((PWBOBJ)pwbo, ntab));
}

ZEND_FUNCTION(wbtemp_set_value)
{
	LONG pwbo, value, item = 0, subitem = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "ll|ll", &pwbo, &value, &item, &subitem) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL();

	((PWBOBJ)pwbo)->item = item;
	((PWBOBJ)pwbo)->subitem = subitem;

	RETURN_BOOL(wbSetValue((PWBOBJ)pwbo, value));
}

ZEND_FUNCTION(wbtemp_set_range)
{
	LONG pwbo, min = 0, max = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "ll|l", &pwbo, &min, &max) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL();

	RETURN_BOOL(wbSetRange((PWBOBJ)pwbo, min, max));
}

ZEND_FUNCTION(wbtemp_create_item)
{
	char *s;
	int s_len;
	LONG pwbo, param = 0;

	TCHAR *wcs = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "ls|l", &pwbo, &s, &s_len, &param) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()
	else {
		wcs = Utf82WideChar(s, s_len);
		RETURN_BOOL(wbCreateItem((PWBOBJ)pwbo, wcs));
	}
}

ZEND_FUNCTION(wbtemp_create_statusbar_items)
{
	LONG pwbo, clear, param;
	zval *zitems = NULL;
	BOOL bRet = TRUE;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "lz!|ll", &pwbo, &zitems, &clear, &param) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()

	switch(zitems->type) {

		case IS_ARRAY:
			{
				zval *zitem;
				int i;
				int nParts = 0;
				int aWidths[255];
				LPTSTR pszCaption;
				LONG nWidth;

				// Count array elements

				while((zitem = process_array(zitems, TSRMLS_C)) != NULL) {
					parse_array(zitem, "");
					nParts++;
				}

				// Create the array of widths

				i = 0;
				while((zitem = process_array(zitems, TSRMLS_C)) != NULL) {
					parse_array(zitem, "sl", &pszCaption, &nWidth);

					if((i == nParts - 1) && (nWidth <= 0)) {
						aWidths[i] = -1;
					} else {
						if(nWidth <= 4) {

							if(*pszCaption) {
								SIZE siz;

								if(wbGetTextSize(&siz, pszCaption, 0))
									nWidth = siz.cx + 10;	// This number is and arbitrary
								else
									nWidth = 10;
							} else {
								nWidth = 4;
							}
						}
						aWidths[i] = (i ? aWidths[i - 1] : 0) + nWidth;
					}

					i++;
				}
				wbSetStatusBarParts((PWBOBJ)pwbo, nParts, aWidths);

				// Set the text of the various parts

				i = 0;
				while((zitem = process_array(zitems, TSRMLS_C)) != NULL) {
					parse_array(zitem, "sl", &pszCaption, NULL);

					if(!wbSetText((PWBOBJ)pwbo, pszCaption, i, FALSE))
						bRet = FALSE;
					i++;
				}
			}
			RETURN_BOOL(bRet);

		default:
			zend_error(E_WARNING, "Parameter 2 expected to be an array in function %s()",
				get_active_function_name(TSRMLS_C));
			RETURN_NULL();
	}
}

ZEND_FUNCTION(wbtemp_set_text)
{
	LONG pwbo, item = 0;
	BOOL ret = TRUE;
    zval *zcaption;
	char *caption = "";
	char *tooltip = "";

	TCHAR *wcsCaption = 0;
	TCHAR *wcsTooltip = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "lz|l", &pwbo, &zcaption, &item) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()

	switch(zcaption->type) {

		case IS_ARRAY:
			parse_array(zcaption, "ss", &caption, &tooltip);
			if(caption) {
				wcsCaption = Utf82WideChar(caption, 0);
				ret = wbSetText((PWBOBJ)pwbo, wcsCaption, item, FALSE);
			}
			if(tooltip) {
				wcsTooltip = Utf82WideChar(tooltip, 0);
				wbSetText((PWBOBJ)pwbo, wcsTooltip, item, TRUE);
			}
			break;

		case IS_STRING:
			//caption = zcaption->value.str.val;
			wcsCaption = Utf82WideChar(zcaption->value.str.val, zcaption->value.str.len);
			ret = wbSetText((PWBOBJ)pwbo, wcsCaption, item, FALSE);
			wbFree(wcsCaption);
			break;

		case IS_NULL:
			*caption = '\0';
			break;
	}

	RETURN_BOOL(ret);
}

/*
ZEND_FUNCTION(wbtemp_set_text)
{
	LONG pwbo, item = 0;
	BOOL ret = TRUE;
    zval *zcaption;
	char *caption = "";
	char *tooltip = "";

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "lz|l", &pwbo, &zcaption, &item) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()

	switch(zcaption->type) {

		case IS_ARRAY:
			parse_array(zcaption, "ss", &caption, &tooltip);
			break;

		case IS_STRING:
			caption = zcaption->value.str.val;
			break;

		case IS_NULL:
		case IS_LONG:
		case IS_DOUBLE:
		case IS_BOOL:
		case IS_CONSTANT:
//		default:
//		case IS_LONG:
//		case IS_BOOL:
//		case IS_FLOAT:
			printf("%d   ", zcaption->value.lval);
			convert_to_string_ex(&zcaption);
			printf("[%s]\n", zcaption->value.str.val);
			if(zcaption->value.str.val)
				caption = zcaption->value.str.val;
			else
				*caption = '\0';
			break;

//		case IS_NULL:
		default:
			*caption = '\0';
			break;
	}

	// ????????????<<<<<<<     Convert line breaks for the caption and tooltip

	if((!caption || !*caption) && zcaption->type == IS_STRING) {
		ret = wbSetText((PWBOBJ)pwbo, "", item, FALSE);
	} else if(caption && *caption) {
		ret = wbSetText((PWBOBJ)pwbo, caption, item, FALSE);
	}

	// Set tooltip

	if(tooltip && *tooltip) {
		ret = wbSetText((PWBOBJ)pwbo, tooltip, item, TRUE);
	}

	RETURN_BOOL(ret);
}
*/

ZEND_FUNCTION(wbtemp_get_text)
{
	TCHAR *ptext;
	LONG pwbo;
	int len, index = -1;

	char *str = 0;
	int str_len = 0;

	// NOTE: I don't quite understand why do I need all these
	// len + 1 and len - 1 stuff below, but it works

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	 "l|l", &pwbo, &index) == FAILURE)
		return;

	if(!wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()				// This is an error, so return NULL

	len = wbGetTextLength((PWBOBJ)pwbo, index) + 1;
	if(len) {
		ptext = emalloc(sizeof(TCHAR) * (len + 1));
		if(!ptext) {
			RETURN_NULL()			// This is an error, so return NULL
		} else {
			wbGetText((PWBOBJ)pwbo, ptext, len, index);
			if(*ptext) {
				str = WideChar2Utf8(ptext, &str_len);
				RETURN_STRINGL(str, max(0, str_len - 1), TRUE)
			} else
				RETURN_STRING("", TRUE)	// This is a valid empty string
		}
	} else {
		RETURN_STRING("", TRUE)	// This is a valid empty string
	}
}

//------------------------------------------------------------------ END OF FILE
