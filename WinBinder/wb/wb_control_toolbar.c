/*******************************************************************************

 WINBINDER - The native Windows binding for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 Toolbar control

*******************************************************************************/

//----------------------------------------------------------------- DEPENDENCIES

#include "wb.h"

//---------------------------------------------------------- FUNCTION PROTOTYPES

// Static

static HWND CreateToolbar(HWND hwndParent, int nButtons, int nBtnWidth, int nBtnHeight, HBITMAP hbm);
static BOOL CreateToolbarButton(HWND hwnd, int id, int nIndex, LPCTSTR pszHint);

// External

extern void SetToolBarHandle(HWND hCtrl);
extern BOOL IsBitmap(HANDLE handle);

//----------------------------------------------------------- EXPORTED FUNCTIONS

PWBOBJ wbCreateToolbar(PWBOBJ pwboParent, PWBITEM pitem[], int nItems, int nBtnWidth, int nBtnHeight, HBITMAP hbm)
{
	int i;
	PWBOBJ pwbo;
	HWND hToolbar;

	if(!pwboParent || !pwboParent->hwnd || !IsWindow(pwboParent->hwnd))
		return NULL;

	// Create the toolbar

	hToolbar = CreateToolbar((HWND)pwboParent->hwnd, nItems, nBtnWidth, nBtnHeight, hbm);
	if(!hToolbar) {
		wbError(TEXT(__FUNCTION__), MB_ICONWARNING, TEXT("Could not create toolbar"));
		return NULL;
	}

	pwbo = wbMalloc(sizeof(WBOBJ));
	pwbo->hwnd = hToolbar;
	pwbo->id = 0;
	pwbo->uClass = ToolBar;
	pwbo->item = -1;
	pwbo->subitem = -1;
	pwbo->style = 0;
	pwbo->pszCallBackFn = NULL;
	pwbo->pszCallBackObj = NULL;
	pwbo->lparam = 0;
	pwbo->parent = pwboParent;

	for(i = 0; i < nItems; i++) {
		if(!pitem[i] || !pitem[i]->id)
			CreateToolbarButton(hToolbar, 0, i, NULL);				// Separator
		else
			CreateToolbarButton(hToolbar, pitem[i]->id, pitem[i]->index, pitem[i]->pszHint);
	}

	SetWindowLong(pwbo->hwnd, GWL_USERDATA, (LONG)pwbo);

	return pwbo;
}

//------------------------------------------------------------ PRIVATE FUNCTIONS

static HWND CreateToolbar(HWND hwndParent, int nButtons, int nBtnWidth, int nBtnHeight, HBITMAP hbm)
{
	HWND hTBWnd;			// Handle of toolbar window
	HIMAGELIST imageList;

	// Cria a toolbar

	hTBWnd = CreateWindowEx(0,
      TOOLBARCLASSNAME, TEXT(""),
	  WS_CHILD | WS_VISIBLE | CCS_TOP | TBSTYLE_TOOLTIPS | TBSTYLE_FLAT,
	  0, 0, 0, 0,
      hwndParent, NULL, hAppInstance, NULL
    );

	if(!hTBWnd)
		return NULL;

	SetToolBarHandle(hTBWnd);

	// Create an ImageList with transparent bitmaps

	if(hbm && IsBitmap(hbm)) {

		nButtons = MAX(1, MIN(nButtons, MIN(nBtnWidth, MAX_IMAGELIST_IMAGES)));

		imageList = ImageList_Create(nBtnWidth, nBtnHeight, ILC_COLORDDB | ILC_MASK, nButtons, 0);
		ImageList_AddMasked(imageList, hbm, RGB(0, 255, 0));
		DeleteObject(hbm);
		SendMessage(hTBWnd, TB_SETIMAGELIST, 0, (LPARAM)imageList);
	}

	return hTBWnd;
}

static BOOL CreateToolbarButton(HWND hwnd, int id, int nIndex, LPCTSTR pszHint)
{
	TBBUTTON tbb;
	BOOL bRet;

	tbb.fsState = TBSTATE_ENABLED;
	tbb.dwData = 0;

	if(id == 0) {			// Separator
		tbb.idCommand = 0;
		tbb.fsState = 0;
		tbb.fsStyle = TBSTYLE_SEP;
		tbb.dwData = 0;
		tbb.iBitmap = 0;
		tbb.iString = 0;
	} else {				// Button
		tbb.idCommand = id;
		tbb.fsState = TBSTATE_ENABLED;
		tbb.fsStyle = TBSTYLE_BUTTON;
		if(pszHint && *pszHint) {
			tbb.dwData = (DWORD)_wcsdup(pszHint);
		} else
			tbb.dwData = 0;
		tbb.iBitmap = nIndex;
		tbb.iString = nIndex;
	}

	// Insert the button

	bRet = SendMessage(hwnd, TB_INSERTBUTTON, 32767, (LPARAM)&tbb);
	if(!bRet) {
		if(id)
			wbError(TEXT(__FUNCTION__), MB_ICONWARNING, TEXT("Could not create item # %d in toolbar"), id);
		else
			wbError(TEXT(__FUNCTION__), MB_ICONWARNING, TEXT("Could not create separator in toolbar"));
	}
	return bRet;
}

//------------------------------------------------------------------ END OF FILE
