/*******************************************************************************

 WINBINDER - The native Windows binding for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 Drawing functions

*******************************************************************************/

//----------------------------------------------------------------- DEPENDENCIES

#include "wb.h"

//-------------------------------------------------------------------- VARIABLES

//----------------------------------------------------------- PRIVATE PROTOTYPES

static HDC GetBitmapDC(HBITMAP hbm);
static HDC hdcMain = NULL;
static HANDLE DrawStart(HANDLE handle);
static BOOL DrawEnd(HANDLE handle);
static HPEN CreatePenFromStyle(COLORREF cl, int nLineWidth, int nLineStyle, DWORD dwCaps);

//-------------------------------------------------------------- PIXEL FUNCTIONS

// "handle" may be a PWBOBJ, HWND, HDC or HBITMAP

BOOL wbSetPixel(HANDLE handle, int xPos, int yPos, COLORREF color)
{
    BOOL bResult;

	if(!DrawStart(handle))
		return FALSE;

	bResult = (SetPixel(hdcMain, xPos, yPos, color) != (COLORREF)-1);

	if(!DrawEnd(handle))
		return FALSE;
	return bResult;
}

// "handle" may be a PWBOBJ, HWND, HDC or HBITMAP

COLORREF wbGetPixel(HANDLE handle, int xPos, int yPos)
{
	COLORREF clResult;

	if(!DrawStart(handle))
		return CLR_INVALID;

	clResult = GetPixel(hdcMain, xPos, yPos);

	if(!DrawEnd(handle))
		return CLR_INVALID;
	return clResult;
}

//------------------------------------------------------------ DRAWING FUNCTIONS

BOOL wbDrawBitmap(HANDLE handle, HBITMAP hbmBits, int xPos, int yPos, int nWidth, int nHeight, int xOffset, int yOffset, COLORREF clTransp)
{
	BOOL bRet;
	HDC hdcMem;
	HBITMAP hbmMask = NULL;

	if(!hbmBits)
		return FALSE;

	if(!DrawStart(handle))
		return FALSE;

	if(nWidth < 1 && nHeight < 1) {
		DWORD dwDim;

		dwDim = wbGetImageDimensions(hbmBits);
		nWidth = LOWORD(dwDim);
		nHeight = HIWORD(dwDim);
	}

	if(clTransp != CLR_INVALID)
		hbmMask = wbCreateMask(hbmBits, clTransp);

	hdcMem = CreateCompatibleDC(hdcMain);
	SelectObject(hdcMem, hbmBits);

	if(clTransp != CLR_INVALID) {

		bRet = MaskBlt(hdcMain,
			xPos, yPos, nWidth, nHeight,
			hdcMem,
			xOffset, yOffset,
			hbmMask,
			xOffset, yOffset,
			MAKEROP4(SRCPAINT, SRCCOPY));

	} else {
		bRet = BitBlt(hdcMain, xPos, yPos, nWidth, nHeight, hdcMem, xOffset, yOffset, SRCCOPY);
	}
	DeleteDC(hdcMem);

	if((clTransp != CLR_INVALID) && hbmMask)
		DeleteObject(hbmMask);

	if(!DrawEnd(handle))
		return FALSE;

	return bRet;
}

BOOL wbGetTextSize(PSIZE psizeText, LPCTSTR pszString, int nFont)
{
	HDC hdcTest;
	PFONT pfont;
	HFONT hfOld;
	BOOL bRet;

	if(!psizeText)
		return FALSE;

	pfont = wbGetFont(nFont);
	if(!pfont)
		return FALSE;

	hdcTest = CreateCompatibleDC(hdcMain);
	hfOld = SelectObject(hdcTest, pfont->hFont);
	bRet = GetTextExtentPoint32(hdcTest, (LPTSTR)pszString, wcslen(pszString), psizeText);
	SelectObject(hdcTest, hfOld);
	DeleteDC(hdcTest);
	return bRet;
}

/* Draws the string pszString transparently with the font indexed by nFont. If nWidth and nHeight
  are zero, ignores drawing boundaries and ignores dwFlags */

BOOL wbDrawText(HANDLE handle, LPCTSTR pszString, int xPos, int yPos, int nWidth, int nHeight, int nFont, DWORD dwFlags)
{
	int nLen;
	BOOL bRet;
	COLORREF clOld;
	HFONT hfOld;
	RECT rc;
	PFONT pfont;
	DWORD dwWinFlags;

	if(!pszString || !*pszString)
		return FALSE;

	pfont = wbGetFont(nFont);
	if(!pfont)
		return FALSE;

	if(!DrawStart(handle))
		return FALSE;

	clOld = SetTextColor(hdcMain, pfont->color);
	SetBkMode(hdcMain, TRANSPARENT);
	hfOld = SelectObject(hdcMain, pfont->hFont);
	nLen = wcslen(pszString);

	if(nWidth > 0 && nHeight > 0) {
		rc.left = xPos;
		rc.right = xPos + nWidth;
		rc.top = yPos;
		rc.bottom = yPos + nHeight;

		// Text flags

		if(dwFlags == 0) {												// No flags: default font

			dwWinFlags = DT_LEFT | DT_SINGLELINE | DT_VCENTER;

		} else if(BITTEST(dwFlags, WBC_MULTILINE)) {					// Multi-line

			dwWinFlags = DT_EDITCONTROL | DT_WORDBREAK;

		} else if(BITTEST(dwFlags, WBC_TOP)) {							// Top

			if(BITTEST(dwFlags, WBC_CENTER))
				dwWinFlags = DT_CENTER | DT_SINGLELINE | DT_TOP;
			else if(BITTEST(dwFlags, WBC_RIGHT))
				dwWinFlags = DT_RIGHT | DT_SINGLELINE | DT_TOP;
			else // WBC_LEFT == 0
				dwWinFlags = DT_LEFT | DT_SINGLELINE | DT_TOP;

		} else if(BITTEST(dwFlags, WBC_BOTTOM)) {						// Bottom

			if(BITTEST(dwFlags, WBC_CENTER))
				dwWinFlags = DT_CENTER | DT_SINGLELINE | DT_BOTTOM;
			else if(BITTEST(dwFlags, WBC_RIGHT))
				dwWinFlags = DT_RIGHT | DT_SINGLELINE | DT_BOTTOM;
			else // WBC_LEFT == 0
				dwWinFlags = DT_LEFT | DT_SINGLELINE | DT_BOTTOM;

		} else {														// Middle (WBC_MIDDLE == 0)

			if(BITTEST(dwFlags, WBC_CENTER))
				dwWinFlags = DT_CENTER | DT_SINGLELINE | DT_VCENTER;
			else if(BITTEST(dwFlags, WBC_RIGHT))
				dwWinFlags = DT_RIGHT | DT_SINGLELINE | DT_VCENTER;
			else // WBC_LEFT == 0
				dwWinFlags = DT_LEFT | DT_SINGLELINE | DT_VCENTER;
		}

		if(BITTEST(dwFlags, WBC_ELLIPSIS))
			dwWinFlags |= DT_END_ELLIPSIS | DT_PATH_ELLIPSIS;

		bRet = DrawTextEx(hdcMain, (LPTSTR)pszString, nLen, &rc, dwWinFlags, NULL);
	} else {
		bRet = TextOut(hdcMain, xPos, yPos, (LPTSTR)pszString, nLen);
	}

	SelectObject(hdcMain, hfOld);
	SetBkMode(hdcMain, OPAQUE);
	SetTextColor(hdcMain, clOld);

	if(!DrawEnd(handle))
		return FALSE;

	return bRet;
}

/* Draws a line on the handle. (x0, y0) are the first point coordinates,
  (x1, y1) are the end point coordinates. cl is the line color. */

BOOL wbDrawLine(HANDLE handle, int x0, int y0, int x1, int y1, COLORREF cl, UINT nLineWidth, UINT nLineStyle)
{
	HPEN hpen;
	HGDIOBJ hOldObj;

	if(!DrawStart(handle))
		return FALSE;

	if(cl == CLR_INVALID)
		return FALSE;

	hpen = CreatePenFromStyle(cl, nLineWidth, nLineStyle, PS_ENDCAP_ROUND | PS_JOIN_ROUND);
	hOldObj = SelectObject(hdcMain, hpen);
	MoveToEx(hdcMain, x0, y0, NULL);
	LineTo(hdcMain, x1, y1);
	SelectObject(hdcMain, hOldObj);
	DeleteObject(hpen);

	if(!DrawEnd(handle))
		return FALSE;
	return TRUE;
}

/* Draws a rectangle. xPos, yPos, nWidth and nHeight define the area of the
  rectangle. Set bFilled to TRUE for a filled rectangle, FALSE for a hollow rectangle.
  cl is the fill color of the filled rectangle or the line color of the hollow rectangle.
  */

BOOL wbDrawRect(HANDLE handle, int xPos, int yPos, int nWidth, int nHeight, COLORREF cl, BOOL bFilled, UINT nLineWidth, UINT nLineStyle)
{
	HGDIOBJ hOldObj;

	if(cl == CLR_INVALID)
		return FALSE;

	if(!DrawStart(handle))
		return FALSE;

	if(bFilled) {

		HBRUSH hbr;

		hbr = CreateSolidBrush(cl);
		hOldObj = SelectObject(hdcMain, hbr);

		PatBlt(hdcMain, xPos, yPos, nWidth,	nHeight, PATCOPY); // Faster than FillRect()

		SelectObject(hdcMain, hOldObj);

		DeleteObject(hbr);

	} else {

		HPEN hpen;

		hpen = CreatePenFromStyle(cl, nLineWidth, nLineStyle, PS_ENDCAP_SQUARE | PS_JOIN_MITER);
		hOldObj = SelectObject(hdcMain, hpen);

		MoveToEx(hdcMain, xPos, yPos, NULL);
		LineTo(hdcMain, xPos + nWidth - 1, yPos);
		LineTo(hdcMain, xPos + nWidth - 1, yPos + nHeight - 1);
		LineTo(hdcMain, xPos, yPos + nHeight - 1);
		LineTo(hdcMain, xPos, yPos);

		SelectObject(hdcMain, hOldObj);
		DeleteObject(hpen);
	}

	if(!DrawEnd(handle))
		return FALSE;
	return TRUE;
}

/* Draws an ellipse. xPos, yPos, nWidth and nHeight define the area of the
  ellipse. Set bFilled to TRUE for a filled ellipse, FALSE for a hollow ellipse.
  cl is the fill color of the filled ellipse or the line color of the hollow ellipse.
  */

BOOL wbDrawEllipse(HANDLE handle, int xPos, int yPos, int nWidth, int nHeight, COLORREF cl, BOOL bFilled, UINT nLineWidth, UINT nLineStyle)
{
	if(cl == CLR_INVALID)
		return FALSE;

	if(!DrawStart(handle))
		return FALSE;

	if(bFilled) {

		HBRUSH hbr;
		HBRUSH hbrOld;

		hbr = CreateSolidBrush(cl);
		hbrOld = SelectObject(hdcMain, hbr);

		Ellipse(hdcMain, xPos, yPos, xPos + nWidth, yPos + nHeight);

		SelectObject(hdcMain, hbrOld);
		DeleteObject(hbr);

	} else {

		HPEN hpen;
		HBRUSH hbr;
		HPEN hpenOld;
		HBRUSH hbrOld;


		hbr = GetStockObject(NULL_BRUSH);
		hpen = CreatePenFromStyle(cl, nLineWidth, nLineStyle, PS_ENDCAP_ROUND | PS_JOIN_ROUND);
		hbrOld = SelectObject(hdcMain, hbr);
		hpenOld = SelectObject(hdcMain, hpen);

		Ellipse(hdcMain, xPos, yPos, xPos + nWidth, yPos + nHeight);

		SelectObject(hdcMain, hpenOld);
		SelectObject(hdcMain, hbrOld);
		DeleteObject(hpen);
		DeleteObject(hbr);
	}

	if(!DrawEnd(handle))
		return FALSE;
	return TRUE;
}

//------------------------------------------------------------ PRIVATE FUNCTIONS

/* Return a DC for the bitmap pbm. Must be followed by a call to DeleteDC. */

static HDC GetBitmapDC(HBITMAP hbm)
{
	HDC handle, hdcBmp;

	if(!hbm)
		return NULL;
	handle = GetDC(NULL);
	if(!handle)
		return NULL;
	hdcBmp = CreateCompatibleDC(handle);
	if(!hdcBmp) {
		ReleaseDC(NULL, handle);
		return NULL;
	}
	SelectObject(hdcBmp, hbm);
	ReleaseDC(NULL, handle);
	return hdcBmp;
}

// Sets hdcMain

static HANDLE DrawStart(HANDLE handle)
{
	if(!handle)
		return NULL;

	if(wbIsWBObj(handle, FALSE)) {				// Is the handle a PWBOBJ?
		handle = ((PWBOBJ)handle)->hwnd;
	}
	if(IsWindow((HWND)handle)) {				// Is the handle a window?
		hdcMain = GetDC((HWND)handle);
		if(hdcMain)
			return hdcMain;
	}
	if(GetDeviceCaps(handle, RASTERCAPS)) {		// Is it a DC?
		hdcMain = handle;
		return hdcMain;
	}
	hdcMain = GetBitmapDC((HBITMAP)handle);		// Is it a bitmap?
	if(hdcMain)
		return hdcMain;
	return NULL;								// Unknown
}

static BOOL DrawEnd(HANDLE handle)
{
	if(!handle || !hdcMain)
		return FALSE;

//	if(wbIsWBObj(handle, FALSE))				// Is the handle a PWBOBJ?
//		return TRUE;

	if(IsWindow((HWND)handle)) {				// Is the handle a window?

		if(!hdcMain)
			return FALSE;
		return ReleaseDC((HWND)handle, hdcMain);

	} else {									// Must be a DC

		if(DeleteDC(hdcMain)) {	// *** Should it always delete the DC?
			hdcMain = NULL;		// *** What about a DC that was passed to DrawStart()?
			return TRUE;
		} else
			return FALSE;
	}
}

/* Create a pen according to the parameters. Due to GDI limitations, only single-width lines can have styles. */

static HPEN CreatePenFromStyle(COLORREF cl, int nLineWidth, int nLineStyle, DWORD dwCaps)
{
	LOGBRUSH lb;

	lb.lbStyle = BS_SOLID;
	lb.lbColor = cl;
	lb.lbHatch = 0;

	switch(nLineStyle) {
		case 0:
		default:
			return ExtCreatePen(PS_GEOMETRIC | PS_SOLID | dwCaps,
			  MAX(1, nLineWidth), &lb, 0, NULL);

		case 1:
			return ExtCreatePen(PS_COSMETIC | PS_ALTERNATE | dwCaps,
			  MAX(1, 1), &lb, 0, NULL);

		case 2:
			return ExtCreatePen(PS_COSMETIC | PS_DOT | dwCaps,
			  MAX(1, 1), &lb, 0, NULL);

		case 3:				// Custom style
			{
				DWORD dwStyle[] = {2, 1};
				return ExtCreatePen(PS_COSMETIC | PS_USERSTYLE | dwCaps,
				  MAX(1, 1), &lb, 2, (CONST DWORD *)&dwStyle);
			}

		case 4:				// Custom style
			{
				DWORD dwStyle[] = {2, 2};
				return ExtCreatePen(PS_COSMETIC | PS_USERSTYLE | dwCaps,
				  MAX(1, 1), &lb, 2, (CONST DWORD *)&dwStyle);
			}

		case 5:				// Custom style
			{
				DWORD dwStyle[] = {3, 1};
				return ExtCreatePen(PS_COSMETIC | PS_USERSTYLE | dwCaps,
				  MAX(1, 1), &lb, 2, (CONST DWORD *)&dwStyle);
			}

		case 6:				// Custom style
			{
				DWORD dwStyle[] = {4, 1};
				return ExtCreatePen(PS_COSMETIC | PS_USERSTYLE | dwCaps,
				  MAX(1, 1), &lb, 2, (CONST DWORD *)&dwStyle);
			}

		case 7:
			return ExtCreatePen(PS_COSMETIC | PS_DASH | dwCaps,
			  MAX(1, 1), &lb, 0, NULL);

		case 8:
			return ExtCreatePen(PS_COSMETIC | PS_DASHDOT | dwCaps,
			  MAX(1, 1), &lb, 0, NULL);

		case 9:
			return ExtCreatePen(PS_COSMETIC | PS_DASHDOTDOT | dwCaps,
			  MAX(1, 1), &lb, 0, NULL);
	}
}

//------------------------------------------------------------------ END OF FILE
