/*******************************************************************************

 WINBINDER - The native Windows binding for PHP for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 ZEND wrapper for drawing functions

*******************************************************************************/

//----------------------------------------------------------------- DEPENDENCIES

#include "phpwb.h"

//----------------------------------------------------------- EXPORTED FUNCTIONS

/*
source can be a WinBinder object, a bitmap handle, a window handle or a DC.
Returns NOCOLOR (0xFFFFFFFF) if error
TODO: handle could also be a bitmap file?
*/

ZEND_FUNCTION(wb_get_pixel)
{
	if(ZEND_NUM_ARGS() == 3) {
		LONG handle, x, y;

		if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
		  "lll", &handle, &x, &y) == FAILURE)
			return;

		RETURN_LONG(wbGetPixel((HANDLE)handle, x, y));

	} else {

		// With four parameters: call wbGetPixelDirect() for faster performance (not stable)

		LONG x, y;
		unsigned char *pixdata;
		int pixdata_len;
		BOOL compress4to3 = FALSE;

		if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
		  "slll", &pixdata, &pixdata_len, &x, &y, &compress4to3) == FAILURE)
			return;

		RETURN_LONG(wbGetPixelDirect(pixdata, x, y, compress4to3));
	}
}

/*
source can be a WinBinder object, a bitmap handle, a window handle or a DC.
TODO: allow separate R,G,B values
TODO: handle could also be a bitmap file??
*/

ZEND_FUNCTION(wb_draw_point)
{
    LONG handle, x, y, color;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "llll", &handle, &x, &y, &color) == FAILURE)
		return;

	RETURN_BOOL(wbSetPixel((HANDLE)handle, x, y, color));
}


ZEND_FUNCTION(wb_draw_line)
{
    LONG handle, x0, y0, x1, y1, color, linewidth = 0, linestyle = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "llllll|ll", &handle, &x0, &y0, &x1, &y1, &color, &linewidth, &linestyle) == FAILURE)
		return;

	RETURN_BOOL(wbDrawLine((HANDLE)handle, x0, y0, x1, y1, color, linewidth, linestyle));
}

ZEND_FUNCTION(wb_draw_rect)
{
    LONG handle, x, y, width, height, color, filled = TRUE, linewidth = 0, linestyle = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "llllll|lll", &handle, &x, &y, &width, &height, &color, &filled, &linewidth, &linestyle) == FAILURE)
		return;

	RETURN_BOOL(wbDrawRect((HANDLE)handle, x, y, width, height, color, filled, linewidth, linestyle));
}

ZEND_FUNCTION(wb_draw_ellipse)
{
    LONG handle, x, y, width, height, color, filled = TRUE, linewidth = 0, linestyle = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "llllll|lll", &handle, &x, &y, &width, &height, &color, &filled, &linewidth, &linestyle) == FAILURE)
		return;

	RETURN_BOOL(wbDrawEllipse((HANDLE)handle, x, y, width, height, color, filled, linewidth, linestyle));
}

ZEND_FUNCTION(wb_draw_text)
{
    LONG handle, x, y, width = 0, height = 0, nfont = -1, flags = 0;
    char *text;
    int text_len;
    int nargs;

    TCHAR *wcs = 0;

	nargs = ZEND_NUM_ARGS();
    if(zend_parse_parameters(nargs TSRMLS_CC,
	  "lsll|llll", &handle, &text, &text_len, &x, &y, &width, &height, &nfont, &flags) == FAILURE)
		return;

	switch(nargs) {

		case 4:
			nfont = -1;
			break;

		case 5:
			nfont = width;
			break;
	}

	// UGLY, DIRTY HACK that is necessary to change the unnatural default parameters of wbDrawText.
	// Must change the internal defaults in font functions from the API layer.

	nfont = (nfont == 0 ? -1 : (nfont < 0 ? 0 : nfont));

	wcs = Utf82WideChar(text, text_len);
	RETURN_BOOL(wbDrawText((HANDLE)handle, wcs, x, y, width, height, nfont, flags));
}

ZEND_FUNCTION(wb_draw_image)
{
    LONG handle, hbm, x = 0, y = 0, w = 0, h = 0, cx = 0, cy = 0;
    COLORREF transpcolor = NOCOLOR;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "ll|lllllll", &handle, &hbm, &x, &y, &w, &h, &transpcolor, &cx, &cy) == FAILURE)
		return;

	RETURN_BOOL((LONG)wbDrawBitmap((HANDLE)handle, (HBITMAP)hbm, x, y, w, h, cx, cy, transpcolor))
}

//------------------------------------------------------------------ END OF FILE
