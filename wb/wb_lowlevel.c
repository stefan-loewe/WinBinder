/*******************************************************************************

 WINBINDER - The native Windows binding for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 Low-level functions

*******************************************************************************/

//----------------------------------------------------------------- DEPENDENCIES

#include "wb.h"

//-------------------------------------------------------------------- CONSTANTS

static HMODULE hLastDLL = NULL;

//-------------------------------------------------------------------- VARIABLES

//----------------------------------------------------------- PRIVATE PROTOTYPES

//------------------------------------------------------------- PUBLIC FUNCTIONS

LPARAM wbSendMessage(PWBOBJ pwbo, UINT uMsg, WPARAM wParam, LPARAM lParam)
{
	if((LONG)pwbo == (LONG)HWND_BROADCAST)
		return SendMessage(HWND_BROADCAST, uMsg, wParam, lParam);

	if(!pwbo || !pwbo->hwnd || !IsWindow(pwbo->hwnd))
		return 0;

	return SendMessage((HWND)pwbo->hwnd, uMsg, wParam, lParam);
}

/*
 Returns a handle to the library pszLibName. If pszLibName is NULL then uses last
 library called. Expands names until finds the library or returns NULL if no such
 library was found. The library pointer must be freed with wbReleaseLibrary.
*/

HMODULE wbLoadLibrary(LPCTSTR pszLibName)
{
	HMODULE hLib = NULL;
	TCHAR szName[256];

	if(!pszLibName || !*pszLibName)
		return NULL;

	// "DLL" is automatically appended by LoadLibrary()

	hLib = LoadLibrary(pszLibName);
	if(!hLib) {
		wsprintf(szName, TEXT("%s32"), pszLibName);
		hLib = LoadLibrary(szName);
		if(!hLib) {
			wsprintf(szName, TEXT("%s.exe"), pszLibName);
			hLib = LoadLibrary(szName);
			if(!hLib) {
				wsprintf(szName, TEXT("%s32.exe"), pszLibName);
				hLib = LoadLibrary(szName);
			}
		}
	}

	hLastDLL = hLib;
	return hLib;
}

/*
 Returns a pointer to the library function pszFunction in the library pszLibName.
 Expands names until finds the function or returns NULL.
 Function names are limited to 255 characters.
*/

FARPROC wbGetLibraryFunction(HMODULE hLib, LPCSTR pszFunction)
{
	FARPROC pFn;
	char szName[256];
	int i;

	if(!hLib)
		if(hLastDLL)
			hLib = hLastDLL;

	if(!hLib)
		return NULL;

	// Try to obtain the function address "as is"

	pFn = GetProcAddress(hLib, pszFunction);
	if(pFn)
		return pFn;

	// Try appending an 'A' to the function name

	strcpy(szName, pszFunction);
	strcat(szName, "A");
	pFn = GetProcAddress(hLib, szName);
	if(pFn)
		return pFn;

	// Try appending a 'W' to the function name

	strcpy(szName, pszFunction);
	strcat(szName, "W");
	pFn = GetProcAddress(hLib, szName);
	if(pFn)
		return pFn;

	// Try prepending a '_' to the function name

	strcpy(szName, "_");
	strcat(szName, pszFunction);
	pFn = GetProcAddress(hLib, szName);
	if(pFn)
		return pFn;

	// Try prepending a '_' and appending an 'A' to the function name

	strcpy(szName, "_");
	strcat(szName, pszFunction);
	strcat(szName, "A");
	pFn = GetProcAddress(hLib, szName);
	if(pFn)
		return pFn;

	// Try prepending a '_' and appending a 'W' to the function name

	strcpy(szName, "_");
	strcat(szName, pszFunction);
	strcat(szName, "W");
	pFn = GetProcAddress(hLib, szName);
	if(pFn)
		return pFn;

	// Try appending '@' plus the number of parameters

	for(i = 0; i <= 20; i++) {
		// 2016_08_12 - Jared Allard: be safe with sprintf_s, add support later.
		sprintf(szName, "%s@%d", pszFunction, i * 4);
		pFn = GetProcAddress(hLib, szName);
		if(pFn)
			return pFn;
	}

	// Try prepending a '_' and appending '@' plus the number of parameters

	for(i = 0; i <= 20; i++) {
		sprintf(szName, "_%s@%d", pszFunction, i * 4);
		pFn = GetProcAddress(hLib, szName);
		if(pFn)
			return pFn;
	}

	// Nothing else to try

	return NULL;
}

/* Releases the library handle hLib. hLib must have been obtained with wbLoadLibrary. */

BOOL wbReleaseLibrary(HMODULE hLib)
{
	if(hLib) {
		FreeLibrary(hLib);
		hLastDLL = NULL;
		return TRUE;
	} else {
		return FALSE;
	}
}

//------------------------------------------------------------------ END OF FILE
