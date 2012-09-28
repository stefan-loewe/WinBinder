/*******************************************************************************

 WINBINDER - The native Windows binding for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 Implements HyperLink control

*******************************************************************************/

//----------------------------------------------------------------- DEPENDENCIES

#include "wb.h"

//-------------------------------------------------------------------- CONSTANTS

#define COLOR_HYPERLINK		RGB(0, 0, 192)

//---------------------------------------------------------- FUNCTION PROTOTYPES

extern WNDPROC lpfnHyperLinkProcOld;

static BOOL DrawHyperLink(HDC hdc, HWND hwnd, LPRECT lprc, COLORREF color);

//-------------------------------------------------------------------- VARIABLES

static BOOL bUnderline = FALSE;

//----------------------------------------------------------- EXPORTED FUNCTIONS

// Code adapted from Neal Stublen, thanks to Davide
// http://www.codeguru.com/Cpp/controls/staticctrl/article.php/c5803/

LRESULT CALLBACK HyperLinkProc(HWND hwnd, UINT msg, WPARAM wParam, LPARAM lParam)
{
	switch(msg) {

		case WM_PAINT:
			{
				HDC hdc;
				PAINTSTRUCT ps;
				PWBOBJ pwbobj = wbGetWBObj(hwnd);

				if(!pwbobj)
					break;
				hdc = BeginPaint(hwnd, &ps);
				DrawHyperLink(hdc, hwnd, &ps.rcPaint,
				  pwbobj->lparam == NOCOLOR ? COLOR_HYPERLINK : pwbobj->lparam);
				EndPaint(hwnd, &ps);
			}
			return 0;

		case WM_MOUSEMOVE:

			if(GetCapture() != hwnd) {
				bUnderline = TRUE;
				InvalidateRect(hwnd, NULL, FALSE);
				SetCapture(hwnd);
			} else {
				RECT rect;
				POINT pt;

				GetWindowRect(hwnd, &rect);
				pt.x = LOWORD(lParam);
				pt.y = HIWORD(lParam);
				ClientToScreen(hwnd, &pt);

				if(!PtInRect(&rect, pt)) {
					bUnderline = FALSE;
					InvalidateRect(hwnd, NULL, FALSE);
					ReleaseCapture();
				}
			}
			break;

		case WM_SETCURSOR:
			{
				PWBOBJ pwbo = wbGetWBObj(hwnd);
				if(!pwbo)
					break;

				if(M_nMouseCursor != 0) {
					SetCursor(M_nMouseCursor == -1 ? 0 : (HCURSOR)M_nMouseCursor);
					return TRUE;			// Must return here, not break
				} else {
					break;					// Normal behavior
				}
			}
			break;
	}

	return CallWindowProc(lpfnHyperLinkProcOld, hwnd, msg, wParam, lParam);
}

//------------------------------------------------------------ PRIVATE FUNCTIONS

/*static HFONT GetUnderlineFont(HFONT hOrigFont)
{
	HFONT hUnderFont;
	LOGFONT lf;

	GetObject(hOrigFont, sizeof(LOGFONT), &lf);
	lf.lfUnderline = TRUE;
	hUnderFont = CreateFontIndirect(&lf);
	return hUnderFont;
}
*/

static BOOL DrawHyperLink(HDC hdc, HWND hwnd, LPRECT lprc, COLORREF color)
{
	TCHAR szString[1024];
	int nLen;
	COLORREF clOld;
	HFONT hFont, hfOld;
	HBRUSH hbr, hbrOld;
	BOOL bRet;
	PWBOBJ pwbobj = wbGetWBObj(hwnd);

	GetWindowText(hwnd, szString, 1023);
	nLen = wcslen(szString);
	if(!nLen)
		return FALSE;

	// Draw a background rectangle

	hbr = CreateSolidBrush(GetSysColor(COLOR_BTNFACE));
	hbrOld = SelectObject(hdc, hbr);
	PatBlt(hdc, lprc->left, lprc->top, lprc->right - lprc->left, lprc->bottom - lprc->top,
	  PATCOPY);
	SelectObject(hdc, hbrOld);
	DeleteObject(hbr);

	// Prepare to draw text

	hFont = (HFONT)SendMessage(hwnd, WM_GETFONT, 0, 0);
	clOld = SetTextColor(hdc, color);
	SetBkMode(hdc, TRANSPARENT);
	hfOld = SelectObject(hdc, hFont);

	// Draw the text

	bRet = DrawTextEx(hdc, szString, nLen, lprc,
	  (BITTEST(pwbobj->style, WBC_CENTER) ? DT_CENTER : DT_LEFT) | DT_SINGLELINE, NULL);

	// Draw a line under the text

	if(bUnderline && pwbobj && (BITTEST(pwbobj->style, WBC_LINES))) {
		SIZE siz;

		if(GetTextExtentPoint32(hdc, szString, nLen, &siz)) {
			int xstart;

			if(BITTEST(pwbobj->style, WBC_CENTER))
				xstart = lprc->left + ((lprc->right - lprc->left) - siz.cx) / 2;
			else
				xstart = lprc->left;

			wbDrawLine(hdc,
			  xstart,
			  lprc->top + siz.cy - 1,
			  xstart + siz.cx, lprc->top + siz.cy - 1, color, 0, 0);
		}
	}

	// Restore the original context

	SelectObject(hdc, hfOld);
	SetBkMode(hdc, OPAQUE);
	SetTextColor(hdc, clOld);

	return bRet;
}

//------------------------------------------------------------------ END OF FILE
