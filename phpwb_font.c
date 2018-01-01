/*******************************************************************************

 WINBINDER - The native Windows binding for PHP for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 ZEND wrapper for font functions

*******************************************************************************/

//----------------------------------------------------------------- DEPENDENCIES

#include "phpwb.h"

//----------------------------------------------------------- EXPORTED FUNCTIONS

// Creates a font and stores it in the font cache

ZEND_FUNCTION(wb_create_font)
{
	zend_long height = 10, color = 0x000000, flags = 0;
    char *name;
    int name_len;

    TCHAR *wcs = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "sl|ll", &name, &name_len, &height, &color, &flags) == FAILURE)
		return;

    wcs = Utf82WideChar(name, name_len);
	RETURN_LONG(wbCreateFont(wcs, height, color, flags));
}

// Destroys a font or all created fonts

ZEND_FUNCTION(wb_destroy_font)
{
	zend_long nfont = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "|l", &nfont) == FAILURE)
		return;

	RETURN_BOOL(wbDestroyFont(nfont));
}

// Applies a font to a control

ZEND_FUNCTION(wb_set_font)
{
	zend_long pwbo, nfont = 0, redraw;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "l|ll", &pwbo, &nfont, &redraw) == FAILURE)
		return;

	RETURN_BOOL(wbSetControlFont((PWBOBJ)pwbo, nfont, redraw));
}

//------------------------------------------------------------------ END OF FILE
