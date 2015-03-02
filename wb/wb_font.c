/*******************************************************************************

 WINBINDER - The native Windows binding for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 Font functions

*******************************************************************************/

//----------------------------------------------------------------- DEPENDENCIES

#include "wb.h"

//-------------------------------------------------------------------- CONSTANTS

#define MAX_FONTS			256

//-------------------------------------------------------------------- VARIABLES

// Local

static PFONT pFonts[MAX_FONTS];			// Font cache -- font index zero is the system font
static int nInstalledFonts = 0;
static int nLastFont = 0;				// Zero means no font

//----------------------------------------------------------- EXPORTED FUNCTIONS

/* Sets the font of control pwbobj. Returns the index of the created font or
  zero if failed. */

int wbCreateFont(LPCTSTR pszName, int nHeight, COLORREF color, DWORD dwFlags)
{
	HFONT hFont;
	HDC hdc;

	if(nInstalledFonts >= (MAX_FONTS - 1))
		return 0;

	// Convert points to pixels

    hdc = GetDC(NULL);
    nHeight = -MulDiv(nHeight, GetDeviceCaps(hdc, LOGPIXELSY), 72);
    ReleaseDC(NULL, hdc);

	// Create font with attributes

	hFont = CreateFont(nHeight,
	    0, 0, 0,
		dwFlags & FTA_BOLD ? FW_BOLD : FW_NORMAL,
		dwFlags & FTA_ITALIC,
		dwFlags & FTA_UNDERLINE,
		0, DEFAULT_CHARSET, 0,0,0,0,
		pszName);

	// Store font in cache

	if(hFont) {
		nInstalledFonts++;
		nLastFont = nInstalledFonts;
		pFonts[nLastFont] = wbMalloc(sizeof(FONT));
		if(!pFonts[nInstalledFonts])
			return 0;
		pFonts[nLastFont]->pszName = wbMalloc(sizeof(TCHAR) * (wcslen(pszName) + 1));
		if(!pFonts[nLastFont]->pszName)
			return 0;
		wcscpy(pFonts[nLastFont]->pszName, pszName);
		pFonts[nLastFont]->nHeight = nHeight;
		pFonts[nLastFont]->color = color;
		pFonts[nLastFont]->dwFlags = dwFlags;
		pFonts[nLastFont]->hFont = hFont;
	} else
		return 0;

	return nLastFont;
}

/* Sets or resets the font of control pwbobj.

	If nFont  > 0: Sets the font.
	If nFont == 0: Resets the font to the system font.
	If nFont  < 0: Uses last font used.

  TODO: change text color
*/

BOOL wbSetControlFont(PWBOBJ pwbo, int nFont, BOOL bRedraw)
{
	if(!wbIsWBObj(pwbo, TRUE))					// Is it a valid control?
		return FALSE;

	if(!IsWindow(pwbo->hwnd))
		return FALSE;

	if(nFont > nInstalledFonts)
		return FALSE;

	if(nFont == 0) {				// Resets control to the system font
		SendMessage(pwbo->hwnd, WM_SETFONT, (WPARAM)hIconFont, MAKELPARAM(bRedraw, 0));
	} else if(nFont < 0) {
		nFont = nLastFont;			// Uses last font
	} else {
		// Set the font indexed by nFont
		SendMessage(pwbo->hwnd, WM_SETFONT, (WPARAM)pFonts[nFont]->hFont,
	  	MAKELPARAM(bRedraw, 0));
		nLastFont = nFont;
	}

/*
	// Doesn't work, should intercept WM_CTLCOLOR messages
	{
		HDC hdc;
	    hdc =  GetWindowDC(pwbo->hwnd);
		SetTextColor(hdc, pFonts[nFont]->color);
//		printf("%d %d %08X\n", hdc, pwbo->hwnd, pFonts[nFont]->color);
	    ReleaseDC(pwbo->hwnd, hdc);
	}
*/
	return TRUE;
}

/* Returns the font indexed by nFont.

	If nFont  > 0: Returns the requested font index.
	If nFont == 0: Returns the system font.
	If nFont  < 0: Returns the last font used.
*/

PFONT wbGetFont(int nFont)
{
	if(nFont > nInstalledFonts)
		return NULL;

	if(nFont > nInstalledFonts)
		return FALSE;

	if(nFont == 0) {
		;
	} else if(nFont < 0)
		nFont = nLastFont;
	else
		nLastFont = nFont;

	if(nFont == 0) {

		// Asks for pFonts[0]: set it to system font on first call

		static BOOL bSysFontSet = FALSE;

		if(!bSysFontSet) {
			pFonts[0] = wbMalloc(sizeof(FONT));
			pFonts[0]->hFont = hIconFont;
			pFonts[0]->color = 0x000000;
			pFonts[0]->pszName = TEXT("");
			pFonts[0]->nHeight = 0;
			pFonts[0]->dwFlags = 0;
			bSysFontSet = TRUE;
		}
	}

	return pFonts[nFont];
}

/* Destroys the font indexed by nFont.

	If nFont  > 0: Destroys the requested font.
	If nFont == 0: Destroys all current fonts.
	If nFont  < 0: Does nothing.
*/

BOOL wbDestroyFont(int nFont)
{
	if(nFont > nInstalledFonts)
		return FALSE;

	if(nFont > 0) {

		if(nFont > nInstalledFonts)
			return FALSE;
		wbFree(pFonts[nFont]->pszName);
		if(!DeleteObject(pFonts[nFont]->hFont))
			return FALSE;
		wbFree(pFonts[nFont]);
		nInstalledFonts--;
		return TRUE;

	} else if(nFont == 0) {

		// Destroy all fonts

		int i;

		for(i = 1; i < nInstalledFonts; i++) {		// First font is 1, not zero
			wbFree(pFonts[i]->pszName);
			if(!DeleteObject(pFonts[i]->hFont))
				return FALSE;
			wbFree(pFonts[i]);
		}
		nInstalledFonts = 0;
		return TRUE;
	} else
		return FALSE;
}

//------------------------------------------------------------------ END OF FILE
