/*******************************************************************************

 WINBINDER - The native Windows binding for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 Menu functions

*******************************************************************************/

//----------------------------------------------------------------- DEPENDENCIES

#include "wb.h"

//----------------------------------------------------------- EXPORTED FUNCTIONS

/*
 Receive an array of WBITEM items

 TODO: Allow any bitmap or icon. Today it shows only the "checked" image,
 with 13 x 13 bitmaps only. Whites get transparent, colors are faded.
 Solution: use owner-drawn menus
*/

PWBOBJ wbCreateMenu(PWBOBJ pwboParent, PWBITEM pitem[], int nItems)
{
	int i;
	MENUITEMINFO mi;
	PWBOBJ pwbo;
	HMENU hMenu, hPopup = NULL;
	LPCTSTR pszLastPopup = NULL;

	if(!pwboParent || !pwboParent->hwnd || !IsWindow(pwboParent->hwnd))
		return NULL;

	// Start building the menu

	hMenu = CreateMenu();

	for(i = 0; i < nItems; i++) {

		if(!pitem[i]) {

			AppendMenu(hPopup, MF_SEPARATOR, 0, NULL);

		} else if(!pitem[i]->id) {

			if(pitem[i]->pszCaption && *pitem[i]->pszCaption) {		// Attach a pop-up menu to a top-level menu
				if(hPopup && pszLastPopup) {
					AppendMenu(hMenu, MF_POPUP, (UINT)hPopup, pszLastPopup);
				}
				hPopup = CreateMenu();
				pszLastPopup = pitem[i]->pszCaption;
			} else {												// Separator
				AppendMenu(hPopup, MF_SEPARATOR, 0, NULL);
			}

		} else {

			if(pitem[i]->pszCaption && *pitem[i]->pszCaption) {		// Create a submenu item
				AppendMenu(hPopup, MF_STRING, pitem[i]->id, pitem[i]->pszCaption);
				if(pitem[i]->pszImage && *pitem[i]->pszImage) {
					HBITMAP hImage = wbLoadImage(pitem[i]->pszImage, 0, 0);

					if(hImage)
						SetMenuItemBitmaps(hPopup, pitem[i]->id, MF_BYCOMMAND, hImage, hImage);
				}
			}
		}
	}

	// Create last first-level menu

	if(hPopup && pszLastPopup) {
		AppendMenu(hMenu, MF_POPUP, (UINT)hPopup, pszLastPopup);
	}

	// Attach the menu to the window

	pwbo = wbMalloc(sizeof(WBOBJ));
	pwbo->hwnd = (HWND)hMenu;
	pwbo->id = 0;
	pwbo->uClass = Menu;
	pwbo->item = -1;
	pwbo->subitem = -1;
	pwbo->style = 0;
	pwbo->pszCallBackFn = NULL;
	pwbo->pszCallBackObj = NULL;
	pwbo->lparam = 0;
	pwbo->parent = pwboParent;

	// ********* DOESN'T WORK
	mi.dwItemData = (DWORD)pwbo;
	SetMenuItemInfo((HMENU)pwbo->hwnd, 0, TRUE, &mi);
	// ********* DOESN'T WORK

	SetMenu(pwboParent->hwnd, hMenu);
	return pwbo;
}

/* Sest the text of a menu item */

BOOL wbSetMenuItemText(PWBOBJ pwbo, LPCTSTR pszText)
{
	if(!pwbo || !pwbo->hwnd || !IsMenu((HMENU)pwbo->hwnd))
		return FALSE;

	if(pszText && *pszText)
		return ModifyMenu((HMENU)pwbo->hwnd, pwbo->id, MF_BYCOMMAND | MF_STRING, pwbo->id, pszText);
	else
		return FALSE;
}

BOOL wbGetMenuItemChecked(PWBOBJ pwbo)
{
	MENUITEMINFO mi;

	if(!pwbo || !pwbo->hwnd || !IsMenu((HMENU)pwbo->hwnd))
		return FALSE;

	mi.cbSize = sizeof(MENUITEMINFO);
	mi.fMask = MIIM_STATE;

	GetMenuItemInfo((HMENU)pwbo->hwnd, pwbo->id, FALSE, &mi);
	return mi.fState ? TRUE : FALSE;
}

BOOL wbSetMenuItemChecked(PWBOBJ pwbo, BOOL bState)
{
	if(!pwbo || !pwbo->hwnd || !IsMenu((HMENU)pwbo->hwnd))
		return FALSE;

	/*
	MENUITEMINFO mi;

	mi.cbSize = sizeof(MENUITEMINFO);
	mi.fMask = MIIM_STATE;
	mi.fState = bState ? MFS_CHECKED : MFS_UNCHECKED;

	return SetMenuItemInfo(pwbo->hwnd, pwbo->id, FALSE, &mi);
	*/

	return (CheckMenuItem((HMENU)pwbo->hwnd, pwbo->id, bState ? MF_CHECKED : MF_UNCHECKED) != 0xFFFFFFFF);
}

BOOL wbSetMenuItemSelected(PWBOBJ pwbo)
{
	if(!pwbo || !pwbo->hwnd || !IsMenu((HMENU)pwbo->hwnd))
		return FALSE;

	return CheckMenuRadioItem((HMENU)pwbo->hwnd, pwbo->id, pwbo->id, pwbo->id, MF_BYCOMMAND);
}

/* Insert an image on a menu

 TODO: Allow any bitmap or icon. Today it shows only the "checked" image,
 with 13 x 13 bitmaps only. Whites get transparent, colors are faded.
 Solution: use owner-drawn menus
*/

BOOL wbSetMenuItemImage(PWBOBJ pwbo, HANDLE hImage)
{
	if(!pwbo || !pwbo->hwnd || !IsMenu((HMENU)pwbo->hwnd))
		return FALSE;

	if(hImage)
		return SetMenuItemBitmaps((HMENU)pwbo->hwnd, pwbo->id, MF_BYCOMMAND, (HBITMAP)hImage, (HBITMAP)hImage);
	else
		return FALSE;
}


//------------------------------------------------------------------ END OF FILE
