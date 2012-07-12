/*******************************************************************************

 WINBINDER - The native Windows binding for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 Implements HTML control

 This is basically Jeff Glatt's code from
 http://www.thecodeproject.com/com/cwebpage.asp with much less comments and
 code style changes. Requires IE 5.0 or higher.

*******************************************************************************/

//----------------------------------------------------------------- DEPENDENCIES

#include "wb.h"

#ifndef interface
#define interface struct			// This is missing in exdisp.h in lcc-win32
#endif

#include <exdisp.h>		// For IWebBrowser2
#include <mshtml.h>		// For IHTMLDocument2
#include <mshtmhst.h>	// For IDocHostUIHandler

//-------------------------------------------------------------------- CONSTANTS

// HTML browser commands

#define WEBPAGE_GOBACK			1
#define WEBPAGE_GOFORWARD		2
#define WEBPAGE_GOHOME			3
#define WEBPAGE_SEARCH			4
#define WEBPAGE_REFRESH			5
#define WEBPAGE_STOP			6
#define WEBPAGE_BUSY			7

//-------------------------------------------------------------------- VARIABLES

static const SAFEARRAYBOUND ArrayBound = {1, 0};		// This is used by DisplayHTMLStr(). It can be global because we never change it.

//---------------------------------------------------------- FUNCTION PROTOTYPES

static void ResizeBrowser(HWND hwnd, DWORD width, DWORD height);
static BOOL UnEmbedBrowserObject(HWND);
static BOOL DoPageAction(PWBOBJ pwbo, DWORD action, PVOID pvResult);

//------------------------------------------------------------------------ TYPES

typedef struct {
	IOleInPlaceFrame	frame;			// The IOleInPlaceFrame must be first
	HWND				window;
} _IOleInPlaceFrameEx;

typedef struct {
	IOleInPlaceSite			inplace;	// My IOleInPlaceSite object. Must be first with in _IOleInPlaceSiteEx.
	_IOleInPlaceFrameEx		frame;		// My IOleInPlaceFrame object. Must be first within my _IOleInPlaceFrameEx
} _IOleInPlaceSiteEx;

typedef struct {
	IDocHostUIHandler		ui;			// My IDocHostUIHandler object. Must be first.
} _IDocHostUIHandlerEx;

typedef struct {
	IOleClientSite			client;		// My IOleClientSite object. Must be first.
	_IOleInPlaceSiteEx		inplace;	// My IOleInPlaceSite object. A convenient place to put it.
	_IDocHostUIHandlerEx	ui;			// My IDocHostUIHandler object. Must be first within my _IDocHostUIHandlerEx.
} _IOleClientSiteEx;

//---------------------------------------------------------- FUNCTION PROTOTYPES

// Our IStorage functions that the browser may call

HRESULT STDMETHODCALLTYPE Storage_QueryInterface(IStorage FAR* This, REFIID riid, LPVOID FAR* ppvObj);
ULONG STDMETHODCALLTYPE Storage_AddRef(IStorage FAR* This);
ULONG STDMETHODCALLTYPE Storage_Release(IStorage FAR* This);
HRESULT STDMETHODCALLTYPE Storage_CreateStream(IStorage FAR* This, const WCHAR *pwcsName, DWORD grfMode, DWORD reserved1, DWORD reserved2, IStream **ppstm);
HRESULT STDMETHODCALLTYPE Storage_OpenStream(IStorage FAR* This, const WCHAR * pwcsName, void *reserved1, DWORD grfMode, DWORD reserved2, IStream **ppstm);
HRESULT STDMETHODCALLTYPE Storage_CreateStorage(IStorage FAR* This, const WCHAR *pwcsName, DWORD grfMode, DWORD reserved1, DWORD reserved2, IStorage **ppstg);
HRESULT STDMETHODCALLTYPE Storage_OpenStorage(IStorage FAR* This, const WCHAR * pwcsName, IStorage * pstgPriority, DWORD grfMode, SNB snbExclude, DWORD reserved, IStorage **ppstg);
HRESULT STDMETHODCALLTYPE Storage_CopyTo(IStorage FAR* This, DWORD ciidExclude, IID const *rgiidExclude, SNB snbExclude,IStorage *pstgDest);
HRESULT STDMETHODCALLTYPE Storage_MoveElementTo(IStorage FAR* This, const OLECHAR *pwcsName,IStorage * pstgDest, const OLECHAR *pwcsNewName, DWORD grfFlags);
HRESULT STDMETHODCALLTYPE Storage_Commit(IStorage FAR* This, DWORD grfCommitFlags);
HRESULT STDMETHODCALLTYPE Storage_Revert(IStorage FAR* This);
HRESULT STDMETHODCALLTYPE Storage_EnumElements(IStorage FAR* This, DWORD reserved1, void * reserved2, DWORD reserved3, IEnumSTATSTG ** ppenum);
HRESULT STDMETHODCALLTYPE Storage_DestroyElement(IStorage FAR* This, const OLECHAR *pwcsName);
HRESULT STDMETHODCALLTYPE Storage_RenameElement(IStorage FAR* This, const WCHAR *pwcsOldName, const WCHAR *pwcsNewName);
HRESULT STDMETHODCALLTYPE Storage_SetElementTimes(IStorage FAR* This, const WCHAR *pwcsName, FILETIME const *pctime, FILETIME const *patime, FILETIME const *pmtime);
HRESULT STDMETHODCALLTYPE Storage_SetClass(IStorage FAR* This, REFCLSID clsid);
HRESULT STDMETHODCALLTYPE Storage_SetStateBits(IStorage FAR* This, DWORD grfStateBits, DWORD grfMask);
HRESULT STDMETHODCALLTYPE Storage_Stat(IStorage FAR* This, STATSTG * pstatstg, DWORD grfStatFlag);

// Our IOleInPlaceFrame functions that the browser may call

HRESULT STDMETHODCALLTYPE Frame_QueryInterface(IOleInPlaceFrame FAR* This, REFIID riid, LPVOID FAR* ppvObj);
HRESULT STDMETHODCALLTYPE Frame_AddRef(IOleInPlaceFrame FAR* This);
HRESULT STDMETHODCALLTYPE Frame_Release(IOleInPlaceFrame FAR* This);
HRESULT STDMETHODCALLTYPE Frame_GetWindow(IOleInPlaceFrame FAR* This, HWND FAR* lphwnd);
HRESULT STDMETHODCALLTYPE Frame_ContextSensitiveHelp(IOleInPlaceFrame FAR* This, BOOL fEnterMode);
HRESULT STDMETHODCALLTYPE Frame_GetBorder(IOleInPlaceFrame FAR* This, LPRECT lprectBorder);
HRESULT STDMETHODCALLTYPE Frame_RequestBorderSpace(IOleInPlaceFrame FAR* This, LPCBORDERWIDTHS pborderwidths);
HRESULT STDMETHODCALLTYPE Frame_SetBorderSpace(IOleInPlaceFrame FAR* This, LPCBORDERWIDTHS pborderwidths);
HRESULT STDMETHODCALLTYPE Frame_SetActiveObject(IOleInPlaceFrame FAR* This, IOleInPlaceActiveObject *pActiveObject, LPCOLESTR pszObjName);
HRESULT STDMETHODCALLTYPE Frame_InsertMenus(IOleInPlaceFrame FAR* This, HMENU hmenuShared, LPOLEMENUGROUPWIDTHS lpMenuWidths);
HRESULT STDMETHODCALLTYPE Frame_SetMenu(IOleInPlaceFrame FAR* This, HMENU hmenuShared, HOLEMENU holemenu, HWND hwndActiveObject);
HRESULT STDMETHODCALLTYPE Frame_RemoveMenus(IOleInPlaceFrame FAR* This, HMENU hmenuShared);
HRESULT STDMETHODCALLTYPE Frame_SetStatusText(IOleInPlaceFrame FAR* This, LPCOLESTR pszStatusText);
HRESULT STDMETHODCALLTYPE Frame_EnableModeless(IOleInPlaceFrame FAR* This, BOOL fEnable);
HRESULT STDMETHODCALLTYPE Frame_TranslateAccelerator(IOleInPlaceFrame FAR* This, LPMSG lpmsg, WORD wID);

// Our IDocHostUIHandler functions that the browser may call

HRESULT STDMETHODCALLTYPE UI_QueryInterface(IDocHostUIHandler FAR* This, REFIID riid, void ** ppvObject);
HRESULT STDMETHODCALLTYPE UI_AddRef(IDocHostUIHandler FAR* This);
HRESULT STDMETHODCALLTYPE UI_Release(IDocHostUIHandler FAR* This);
HRESULT STDMETHODCALLTYPE UI_ShowContextMenu(IDocHostUIHandler FAR* This, DWORD dwID, POINT __RPC_FAR *ppt, IUnknown __RPC_FAR *pcmdtReserved, IDispatch __RPC_FAR *pdispReserved);
HRESULT STDMETHODCALLTYPE UI_GetHostInfo(IDocHostUIHandler FAR* This, DOCHOSTUIINFO __RPC_FAR *pInfo);
HRESULT STDMETHODCALLTYPE UI_ShowUI(IDocHostUIHandler FAR* This, DWORD dwID, IOleInPlaceActiveObject __RPC_FAR *pActiveObject, IOleCommandTarget __RPC_FAR *pCommandTarget, IOleInPlaceFrame __RPC_FAR *pFrame, IOleInPlaceUIWindow __RPC_FAR *pDoc);
HRESULT STDMETHODCALLTYPE UI_HideUI(IDocHostUIHandler FAR* This);
HRESULT STDMETHODCALLTYPE UI_UpdateUI(IDocHostUIHandler FAR* This);
HRESULT STDMETHODCALLTYPE UI_EnableModeless(IDocHostUIHandler FAR* This, BOOL fEnable);
HRESULT STDMETHODCALLTYPE UI_OnDocWindowActivate(IDocHostUIHandler FAR* This, BOOL fActivate);
HRESULT STDMETHODCALLTYPE UI_OnFrameWindowActivate(IDocHostUIHandler FAR* This, BOOL fActivate);
HRESULT STDMETHODCALLTYPE UI_ResizeBorder(IDocHostUIHandler FAR* This, LPCRECT prcBorder, IOleInPlaceUIWindow __RPC_FAR *pUIWindow, BOOL fRameWindow);
HRESULT STDMETHODCALLTYPE UI_TranslateAccelerator(IDocHostUIHandler FAR* This, LPMSG lpMsg, const GUID __RPC_FAR *pguidCmdGroup, DWORD nCmdID);
HRESULT STDMETHODCALLTYPE UI_GetOptionKeyPath(IDocHostUIHandler FAR* This, LPOLESTR __RPC_FAR *pchKey, DWORD dw);
HRESULT STDMETHODCALLTYPE UI_GetDropTarget(IDocHostUIHandler FAR* This, IDropTarget __RPC_FAR *pDropTarget, IDropTarget __RPC_FAR *__RPC_FAR *ppDropTarget);
HRESULT STDMETHODCALLTYPE UI_GetExternal(IDocHostUIHandler FAR* This, IDispatch __RPC_FAR *__RPC_FAR *ppDispatch);
HRESULT STDMETHODCALLTYPE UI_TranslateUrl(IDocHostUIHandler FAR* This, DWORD dwTranslate, OLECHAR __RPC_FAR *pchURLIn, OLECHAR __RPC_FAR *__RPC_FAR *ppchURLOut);
HRESULT STDMETHODCALLTYPE UI_FilterDataObject(IDocHostUIHandler FAR* This, IDataObject __RPC_FAR *pDO, IDataObject __RPC_FAR *__RPC_FAR *ppDORet);

// Our IOleInPlaceSite functions that the browser may call

HRESULT STDMETHODCALLTYPE InPlace_QueryInterface(IOleInPlaceSite FAR* This, REFIID riid, void ** ppvObject);
HRESULT STDMETHODCALLTYPE InPlace_AddRef(IOleInPlaceSite FAR* This);
HRESULT STDMETHODCALLTYPE InPlace_Release(IOleInPlaceSite FAR* This);
HRESULT STDMETHODCALLTYPE InPlace_GetWindow(IOleInPlaceSite FAR* This, HWND FAR* lphwnd);
HRESULT STDMETHODCALLTYPE InPlace_ContextSensitiveHelp(IOleInPlaceSite FAR* This, BOOL fEnterMode);
HRESULT STDMETHODCALLTYPE InPlace_CanInPlaceActivate(IOleInPlaceSite FAR* This);
HRESULT STDMETHODCALLTYPE InPlace_OnInPlaceActivate(IOleInPlaceSite FAR* This);
HRESULT STDMETHODCALLTYPE InPlace_OnUIActivate(IOleInPlaceSite FAR* This);
HRESULT STDMETHODCALLTYPE InPlace_GetWindowContext(IOleInPlaceSite FAR* This, LPOLEINPLACEFRAME FAR* lplpFrame,LPOLEINPLACEUIWINDOW FAR* lplpDoc,LPRECT lprcPosRect,LPRECT lprcClipRect,LPOLEINPLACEFRAMEINFO lpFrameInfo);
HRESULT STDMETHODCALLTYPE InPlace_Scroll(IOleInPlaceSite FAR* This, SIZE scrollExtent);
HRESULT STDMETHODCALLTYPE InPlace_OnUIDeactivate(IOleInPlaceSite FAR* This, BOOL fUndoable);
HRESULT STDMETHODCALLTYPE InPlace_OnInPlaceDeactivate(IOleInPlaceSite FAR* This);
HRESULT STDMETHODCALLTYPE InPlace_DiscardUndoState(IOleInPlaceSite FAR* This);
HRESULT STDMETHODCALLTYPE InPlace_DeactivateAndUndo(IOleInPlaceSite FAR* This);
HRESULT STDMETHODCALLTYPE InPlace_OnPosRectChange(IOleInPlaceSite FAR* This, LPCRECT lprcPosRect);

// Our IOleClientSite functions that the browser may call

HRESULT STDMETHODCALLTYPE Site_QueryInterface(IOleClientSite FAR* This, REFIID riid, void ** ppvObject);
HRESULT STDMETHODCALLTYPE Site_AddRef(IOleClientSite FAR* This);
HRESULT STDMETHODCALLTYPE Site_Release(IOleClientSite FAR* This);
HRESULT STDMETHODCALLTYPE Site_SaveObject(IOleClientSite FAR* This);
HRESULT STDMETHODCALLTYPE Site_GetMoniker(IOleClientSite FAR* This, DWORD dwAssign, DWORD dwWhichMoniker, IMoniker ** ppmk);
HRESULT STDMETHODCALLTYPE Site_GetContainer(IOleClientSite FAR* This, LPOLECONTAINER FAR* ppContainer);
HRESULT STDMETHODCALLTYPE Site_ShowObject(IOleClientSite FAR* This);
HRESULT STDMETHODCALLTYPE Site_OnShowWindow(IOleClientSite FAR* This, BOOL fShow);
HRESULT STDMETHODCALLTYPE Site_RequestNewObjectLayout(IOleClientSite FAR* This);

//-------------------------------------------------------------------- VARIABLES

// Our IStorage VTable

IStorageVtbl MyIStorageTable = {
	Storage_QueryInterface,
	Storage_AddRef,
	Storage_Release,
	(void *)Storage_CreateStream,
	(void *)Storage_OpenStream,
	Storage_CreateStorage,
	Storage_OpenStorage,
	Storage_CopyTo,
	Storage_MoveElementTo,
	Storage_Commit,
	Storage_Revert,
	Storage_EnumElements,
	Storage_DestroyElement,
	Storage_RenameElement,
	Storage_SetElementTimes,
	Storage_SetClass,
	Storage_SetStateBits,
	Storage_Stat
};

// Our IStorage structure

IStorage MyIStorage = { &MyIStorageTable };

// Our IOleInPlaceFrame VTable

IOleInPlaceFrameVtbl MyIOleInPlaceFrameTable = {
	Frame_QueryInterface,
	(void *)Frame_AddRef,
	(void *)Frame_Release,
	Frame_GetWindow,
	Frame_ContextSensitiveHelp,
	Frame_GetBorder,
	Frame_RequestBorderSpace,
	Frame_SetBorderSpace,
	Frame_SetActiveObject,
	Frame_InsertMenus,
	Frame_SetMenu,
	Frame_RemoveMenus,
	Frame_SetStatusText,
	Frame_EnableModeless,
	Frame_TranslateAccelerator
};

// Our IOleClientSite VTable

IOleClientSiteVtbl MyIOleClientSiteTable = {
	Site_QueryInterface,
	(void *)Site_AddRef,
	(void *)Site_Release,
	Site_SaveObject,
	Site_GetMoniker,
	Site_GetContainer,
	Site_ShowObject,
	Site_OnShowWindow,
	Site_RequestNewObjectLayout
};

// Our IDocHostUIHandler VTable

IDocHostUIHandlerVtbl MyIDocHostUIHandlerTable =  {
	UI_QueryInterface,
	(void *)UI_AddRef,
	(void *)UI_Release,
	UI_ShowContextMenu,
	UI_GetHostInfo,
	UI_ShowUI,
	UI_HideUI,
	UI_UpdateUI,
	UI_EnableModeless,
	UI_OnDocWindowActivate,
	UI_OnFrameWindowActivate,
	UI_ResizeBorder,
	UI_TranslateAccelerator,
	UI_GetOptionKeyPath,
	UI_GetDropTarget,
	UI_GetExternal,
	UI_TranslateUrl,
	UI_FilterDataObject}
;

// Our IOleInPlaceSite VTable

IOleInPlaceSiteVtbl MyIOleInPlaceSiteTable =  {
	InPlace_QueryInterface,
	(void *)InPlace_AddRef,
	(void *)InPlace_Release,
	InPlace_GetWindow,
	InPlace_ContextSensitiveHelp,
	InPlace_CanInPlaceActivate,
	InPlace_OnInPlaceActivate,
	InPlace_OnUIActivate,
	InPlace_GetWindowContext,
	InPlace_Scroll,
	InPlace_OnUIDeactivate,
	InPlace_OnInPlaceDeactivate,
	InPlace_DiscardUndoState,
	InPlace_DeactivateAndUndo,
	InPlace_OnPosRectChange
};

//----------------------------------------------------------- EXPORTED FUNCTIONS

/* Puts the browser object inside our host window.
 */

BOOL EmbedBrowserObject(PWBOBJ pwbo)
{
	HWND hwnd;
	IOleObject			*browserObject;
	IWebBrowser2		*webBrowser2;
	RECT				rect;
	char				*ptr;
	_IOleClientSiteEx	*_iOleClientSiteEx;

	if(!pwbo)
		return FALSE;
	hwnd = pwbo->hwnd;

	if(!(ptr = (char *)GlobalAlloc(GMEM_FIXED, sizeof(_IOleClientSiteEx) + sizeof(IOleObject *))))
		return FALSE;

	_iOleClientSiteEx = (_IOleClientSiteEx *)(ptr + sizeof(IOleObject *));
	_iOleClientSiteEx->client.lpVtbl = &MyIOleClientSiteTable;
	_iOleClientSiteEx->inplace.inplace.lpVtbl = &MyIOleInPlaceSiteTable;
	_iOleClientSiteEx->inplace.frame.frame.lpVtbl = &MyIOleInPlaceFrameTable;
	_iOleClientSiteEx->inplace.frame.window = hwnd;
	_iOleClientSiteEx->ui.ui.lpVtbl = &MyIDocHostUIHandlerTable;

	if(!OleCreate((REFCLSID)&CLSID_WebBrowser, (IID *)&IID_IOleObject, OLERENDER_DRAW, 0, (IOleClientSite *)_iOleClientSiteEx, &MyIStorage, (void**)&browserObject)) {

		*((IOleObject **)ptr) = browserObject;

		// The original code uses SetWindowLong/GetWindowLong with GWL_USERDATA to store the
		// browser object pointer. we use pwbo->lparams[0] because the former is already used
		// to store the WinBinder object.

		pwbo->lparams[0] = (LONG)ptr;

//		SetWindowLong(hwnd, GWL_USERDATA, (LONG)ptr);

		browserObject->lpVtbl->SetHostNames(browserObject, L"My Host Name", 0);

		GetClientRect(hwnd, &rect);

		if(!OleSetContainedObject((struct IUnknown *)browserObject, TRUE) &&
		  !browserObject->lpVtbl->DoVerb(browserObject, OLEIVERB_SHOW, NULL, (IOleClientSite *)_iOleClientSiteEx, -1, hwnd, &rect) &&
		  !browserObject->lpVtbl->QueryInterface(browserObject, (IID *)&IID_IWebBrowser2, (void**)&webBrowser2)) {
			webBrowser2->lpVtbl->put_Left(webBrowser2, 0);
			webBrowser2->lpVtbl->put_Top(webBrowser2, 0);
			webBrowser2->lpVtbl->put_Width(webBrowser2, rect.right);
			webBrowser2->lpVtbl->put_Height(webBrowser2, rect.bottom);
			webBrowser2->lpVtbl->Release(webBrowser2);
			return TRUE;
		}

		UnEmbedBrowserObject(hwnd);
		return FALSE;
	}

	GlobalFree(ptr);
	return FALSE;
}

/* Takes a string containing some HTML BODY, and displays it in the specified
 * window.
 */

BOOL DisplayHTMLString(PWBOBJ pwbo, LPCTSTR string)
{
	IWebBrowser2	*webBrowser2;
	LPDISPATCH		lpDispatch;
	IHTMLDocument2	*htmlDoc2;
	IOleObject		*browserObject;
	SAFEARRAY		*sfArray;
	VARIANT			myURL;
	VARIANT			*pVar;
	BSTR			bstr;

	if(!pwbo)
		return FALSE;

	browserObject = *((IOleObject **)pwbo->lparams[0]);
//	browserObject = *((IOleObject **)GetWindowLong(hwnd, GWL_USERDATA));
	bstr = 0;

	if(!browserObject->lpVtbl->QueryInterface(browserObject, (IID *)&IID_IWebBrowser2, (void**)&webBrowser2)) {
		VariantInit((VARIANTARG *)&myURL);
		myURL.vt = VT_BSTR;
		myURL.bstrVal = SysAllocString(L"about:blank");
		webBrowser2->lpVtbl->Navigate2(webBrowser2, &myURL, 0, 0, 0, 0);
		VariantClear(&myURL);
		if(!webBrowser2->lpVtbl->get_Document(webBrowser2, &lpDispatch)) {
			if(!lpDispatch->lpVtbl->QueryInterface(lpDispatch, (IID *)&IID_IHTMLDocument2, (void**)&htmlDoc2)) {
				if(NULL != (sfArray = SafeArrayCreate(VT_VARIANT, 1, (SAFEARRAYBOUND *)&ArrayBound))) {
					if(!SafeArrayAccessData(sfArray, (void**)&pVar)) {
						pVar->vt = VT_BSTR;
#ifndef UNICODE
						{
							wchar_t		*buffer;
							DWORD		size;

							size = MultiByteToWideChar(CP_ACP, 0, string, -1, 0, 0);
							if (!(buffer = (wchar_t *)GlobalAlloc(GMEM_FIXED, sizeof(wchar_t) * size)))
								goto bad;
							MultiByteToWideChar(CP_ACP, 0, string, -1, buffer, size);
							bstr = SysAllocString(buffer);
							GlobalFree(buffer);
						}
#else
						bstr = SysAllocString(string);
#endif
						if(NULL != (pVar->bstrVal = bstr)) {
							htmlDoc2->lpVtbl->write(htmlDoc2, sfArray);
							htmlDoc2->lpVtbl->close(htmlDoc2);
						}
					}
					SafeArrayDestroy(sfArray);
				}
bad:			htmlDoc2->lpVtbl->Release(htmlDoc2);
			}
			lpDispatch->lpVtbl->Release(lpDispatch);
		}
		webBrowser2->lpVtbl->Release(webBrowser2);
	}
	if(bstr)
		return TRUE;

	return FALSE;
}


/* Displays a URL, a HTML file on disk, or do a special browser command. */

BOOL DisplayHTMLPage(PWBOBJ pwbo, LPCTSTR pszWebPageName)
{
	IWebBrowser2 *webBrowser2;
	VARIANT myURL;
	IOleObject *browserObject;

	if(!pwbo)
		return FALSE;

	// Special browser commands

	if(!wcsicmp(pszWebPageName,      TEXT("cmd:back")))
		return DoPageAction(pwbo, WEBPAGE_GOBACK, NULL);
	else if(!wcsicmp(pszWebPageName, TEXT("cmd:forward")))
		return DoPageAction(pwbo, WEBPAGE_GOFORWARD, NULL);
//	else if(!stricmp(pszWebPageName, "cmd:home"))
//		return DoPageAction(pwbo, WEBPAGE_GOHOME, NULL);
//	else if(!stricmp(pszWebPageName, "cmd:search"))
//		return DoPageAction(pwbo, WEBPAGE_SEARCH, NULL);
	else if(!wcsicmp(pszWebPageName, TEXT("cmd:refresh")))
		return DoPageAction(pwbo, WEBPAGE_REFRESH, NULL);
	else if(!wcsicmp(pszWebPageName, TEXT("cmd:stop")))
		return DoPageAction(pwbo, WEBPAGE_STOP, NULL);

	else if(!wcsicmp(pszWebPageName, TEXT("cmd:busy"))) {
		BOOL bBusy = FALSE;

		DoPageAction(pwbo, WEBPAGE_BUSY, &bBusy);
//		printf("[%d] ", bBusy);
		return bBusy;
	}

	else if(!wcsicmp(pszWebPageName, TEXT("cmd:blank")))
		return DisplayHTMLString(pwbo, TEXT("about:blank"));

	// Normal page

//	browserObject = *((IOleObject **)GetWindowLong(hwnd, GWL_USERDATA));
	browserObject = *((IOleObject **)pwbo->lparams[0]);

	if(!browserObject)
		return FALSE;

	if(!browserObject->lpVtbl)
		return FALSE;

	if(!browserObject->lpVtbl->QueryInterface)
		return FALSE;

	if(!browserObject->lpVtbl->QueryInterface(browserObject, (IID *)&IID_IWebBrowser2, (void**)&webBrowser2)) {
		VariantInit(&myURL);
		myURL.vt = VT_BSTR;

#ifndef UNICODE
		{
			wchar_t *buffer;
			DWORD size;

			size = MultiByteToWideChar(CP_ACP, 0, pszWebPageName, -1, 0, 0);
			if (!(buffer = (wchar_t *)GlobalAlloc(GMEM_FIXED, sizeof(wchar_t) * size))) goto badalloc;
			MultiByteToWideChar(CP_ACP, 0, pszWebPageName, -1, buffer, size);
			myURL.bstrVal = SysAllocString(buffer);
			GlobalFree(buffer);
		}
#else
		myURL.bstrVal = SysAllocString(pszWebPageName);
#endif

		if (!myURL.bstrVal) {
badalloc:	webBrowser2->lpVtbl->Release(webBrowser2);
			return FALSE;
		}

		webBrowser2->lpVtbl->Navigate2(webBrowser2, &myURL, 0, 0, 0, 0);
		VariantClear(&myURL);
		webBrowser2->lpVtbl->Release(webBrowser2);
		return TRUE;
	}
	return FALSE;
}

//--------------------------------------------------------------- OBJECT METHODS

//----------------------------------------------
// My IDocHostUIHandler functions
//----------------------------------------------

HRESULT STDMETHODCALLTYPE UI_QueryInterface(IDocHostUIHandler FAR* This, REFIID riid, LPVOID FAR* ppvObj)
{
	return(Site_QueryInterface((IOleClientSite *)((char *)This - sizeof(IOleClientSite) - sizeof(_IOleInPlaceSiteEx)), riid, ppvObj));
}

HRESULT STDMETHODCALLTYPE UI_AddRef(IDocHostUIHandler FAR* This)
{
	return(1);
}

HRESULT STDMETHODCALLTYPE UI_Release(IDocHostUIHandler FAR* This)
{
	return(1);
}

// Called when the browser object is about to display its context menu.
HRESULT STDMETHODCALLTYPE UI_ShowContextMenu(IDocHostUIHandler FAR* This, DWORD dwID, POINT __RPC_FAR *ppt, IUnknown __RPC_FAR *pcmdtReserved, IDispatch __RPC_FAR *pdispReserved)
{
	return(S_OK);
}

// Called at initialization of the browser object UI. We can set various features of the browser object here.
HRESULT STDMETHODCALLTYPE UI_GetHostInfo(IDocHostUIHandler FAR* This, DOCHOSTUIINFO __RPC_FAR *pInfo)
{
	pInfo->cbSize = sizeof(DOCHOSTUIINFO);
	pInfo->dwFlags = DOCHOSTUIFLAG_NO3DBORDER;
	pInfo->dwDoubleClick = DOCHOSTUIDBLCLK_DEFAULT;

	return(S_OK);
}

// Called when the browser object shows its UI.
HRESULT STDMETHODCALLTYPE UI_ShowUI(IDocHostUIHandler FAR* This, DWORD dwID, IOleInPlaceActiveObject __RPC_FAR *pActiveObject, IOleCommandTarget __RPC_FAR *pCommandTarget, IOleInPlaceFrame __RPC_FAR *pFrame, IOleInPlaceUIWindow __RPC_FAR *pDoc)
{
	return(S_OK);
}

// Called when browser object hides its UI. This allows us to hide any menus/toolbars we created in ShowUI.
HRESULT STDMETHODCALLTYPE UI_HideUI(IDocHostUIHandler FAR* This)
{
	return(S_OK);
}

// Called when the browser object wants to notify us that the command state has changed.
HRESULT STDMETHODCALLTYPE UI_UpdateUI(IDocHostUIHandler FAR* This)
{
	// We update our UI in our window message loop so we don't do anything here.
	return(S_OK);
}

// Called from the browser object's IOleInPlaceActiveObject object's EnableModeless() function.
HRESULT STDMETHODCALLTYPE UI_EnableModeless(IDocHostUIHandler FAR* This, BOOL fEnable)
{
	return(S_OK);
}

// Called from the browser object's IOleInPlaceActiveObject object's OnDocWindowActivate() function.
HRESULT STDMETHODCALLTYPE UI_OnDocWindowActivate(IDocHostUIHandler FAR* This, BOOL fActivate)
{
	return(S_OK);
}

// Called from the browser object's IOleInPlaceActiveObject object's OnFrameWindowActivate() function.
HRESULT STDMETHODCALLTYPE UI_OnFrameWindowActivate(IDocHostUIHandler FAR* This, BOOL fActivate)
{
	return(S_OK);
}

// Called from the browser object's IOleInPlaceActiveObject object's ResizeBorder() function.
HRESULT STDMETHODCALLTYPE UI_ResizeBorder(IDocHostUIHandler FAR* This, LPCRECT prcBorder, IOleInPlaceUIWindow __RPC_FAR *pUIWindow, BOOL fRameWindow)
{
	return(S_OK);
}

// Called from the browser object's TranslateAccelerator routines to translate key strokes to commands.
HRESULT STDMETHODCALLTYPE UI_TranslateAccelerator(IDocHostUIHandler FAR* This, LPMSG lpMsg, const GUID __RPC_FAR *pguidCmdGroup, DWORD nCmdID)
{
	return(S_FALSE);
}

// Called by the browser object to find where the host wishes the browser to get its options in the registry.
HRESULT STDMETHODCALLTYPE UI_GetOptionKeyPath(IDocHostUIHandler FAR* This, LPOLESTR __RPC_FAR *pchKey, DWORD dw)
{
	// Let the browser use its default registry settings.
	return(S_FALSE);
}

// Called by the browser object when it is used as a drop target

HRESULT STDMETHODCALLTYPE UI_GetDropTarget(IDocHostUIHandler FAR* This, IDropTarget __RPC_FAR *pDropTarget, IDropTarget __RPC_FAR *__RPC_FAR *ppDropTarget)
{
    return(S_FALSE);
}

// Called by the browser when it wants a pointer to our IDispatch object

HRESULT STDMETHODCALLTYPE UI_GetExternal(IDocHostUIHandler FAR* This, IDispatch __RPC_FAR *__RPC_FAR *ppDispatch)
{
	*ppDispatch = 0;
	return(S_FALSE);
}

// Called by the browser object to give us an opportunity to modify the URL to be loaded.

HRESULT STDMETHODCALLTYPE UI_TranslateUrl(IDocHostUIHandler FAR* This, DWORD dwTranslate, OLECHAR __RPC_FAR *pchURLIn, OLECHAR __RPC_FAR *__RPC_FAR *ppchURLOut)
{
	*ppchURLOut = 0;
    return(S_FALSE);
}

// Called by the browser when it does cut/paste to the clipboard

HRESULT STDMETHODCALLTYPE UI_FilterDataObject(IDocHostUIHandler FAR* This, IDataObject __RPC_FAR *pDO, IDataObject __RPC_FAR *__RPC_FAR *ppDORet)
{
	*ppDORet = 0;
	return(S_FALSE);
}

//----------------------------------------------
// My IStorage functions
//----------------------------------------------

HRESULT STDMETHODCALLTYPE Storage_QueryInterface(IStorage FAR* This, REFIID riid, LPVOID FAR* ppvObj)
{
	return E_NOTIMPL;
}

ULONG STDMETHODCALLTYPE Storage_AddRef(IStorage FAR* This)
{
	return(1);
}

ULONG STDMETHODCALLTYPE Storage_Release(IStorage FAR* This)
{
	return(1);
}

HRESULT STDMETHODCALLTYPE Storage_CreateStream(IStorage FAR* This, const WCHAR *pwcsName, DWORD grfMode, DWORD reserved1, DWORD reserved2, IStream **ppstm)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Storage_OpenStream(IStorage FAR* This, const WCHAR * pwcsName, void *reserved1, DWORD grfMode, DWORD reserved2, IStream **ppstm)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Storage_CreateStorage(IStorage FAR* This, const WCHAR *pwcsName, DWORD grfMode, DWORD reserved1, DWORD reserved2, IStorage **ppstg)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Storage_OpenStorage(IStorage FAR* This, const WCHAR * pwcsName, IStorage * pstgPriority, DWORD grfMode, SNB snbExclude, DWORD reserved, IStorage **ppstg)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Storage_CopyTo(IStorage FAR* This, DWORD ciidExclude, IID const *rgiidExclude, SNB snbExclude,IStorage *pstgDest)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Storage_MoveElementTo(IStorage FAR* This, const OLECHAR *pwcsName,IStorage * pstgDest, const OLECHAR *pwcsNewName, DWORD grfFlags)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Storage_Commit(IStorage FAR* This, DWORD grfCommitFlags)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Storage_Revert(IStorage FAR* This)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Storage_EnumElements(IStorage FAR* This, DWORD reserved1, void * reserved2, DWORD reserved3, IEnumSTATSTG ** ppenum)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Storage_DestroyElement(IStorage FAR* This, const OLECHAR *pwcsName)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Storage_RenameElement(IStorage FAR* This, const WCHAR *pwcsOldName, const WCHAR *pwcsNewName)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Storage_SetElementTimes(IStorage FAR* This, const WCHAR *pwcsName, FILETIME const *pctime, FILETIME const *patime, FILETIME const *pmtime)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Storage_SetClass(IStorage FAR* This, REFCLSID clsid)
{
	return(S_OK);
}

HRESULT STDMETHODCALLTYPE Storage_SetStateBits(IStorage FAR* This, DWORD grfStateBits, DWORD grfMask)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Storage_Stat(IStorage FAR* This, STATSTG * pstatstg, DWORD grfStatFlag)
{
	return E_NOTIMPL;
}

//----------------------------------------------
// My IOleClientSite functions
//----------------------------------------------

HRESULT STDMETHODCALLTYPE Site_QueryInterface(IOleClientSite FAR* This, REFIID riid, void ** ppvObject)
{
	if(!memcmp(riid, &IID_IUnknown, sizeof(GUID)) || !memcmp(riid, &IID_IOleClientSite, sizeof(GUID)))
		*ppvObject = &((_IOleClientSiteEx *)This)->client;
	else if (!memcmp(riid, &IID_IOleInPlaceSite, sizeof(GUID)))
		*ppvObject = &((_IOleClientSiteEx *)This)->inplace;
	else if (!memcmp(riid, &IID_IDocHostUIHandler, sizeof(GUID)))
		*ppvObject = &((_IOleClientSiteEx *)This)->ui;
	else {
		*ppvObject = 0;
		return(E_NOINTERFACE);
	}

	return(S_OK);
}

HRESULT STDMETHODCALLTYPE Site_AddRef(IOleClientSite FAR* This)
{
	return(1);
}

HRESULT STDMETHODCALLTYPE Site_Release(IOleClientSite FAR* This)
{
	return(1);
}

HRESULT STDMETHODCALLTYPE Site_SaveObject(IOleClientSite FAR* This)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Site_GetMoniker(IOleClientSite FAR* This, DWORD dwAssign, DWORD dwWhichMoniker, IMoniker ** ppmk)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Site_GetContainer(IOleClientSite FAR* This, LPOLECONTAINER FAR* ppContainer)
{
	*ppContainer = 0;
	return(E_NOINTERFACE);
}

HRESULT STDMETHODCALLTYPE Site_ShowObject(IOleClientSite FAR* This)
{
	return(NOERROR);
}

HRESULT STDMETHODCALLTYPE Site_OnShowWindow(IOleClientSite FAR* This, BOOL fShow)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Site_RequestNewObjectLayout(IOleClientSite FAR* This)
{
	return E_NOTIMPL;
}

//----------------------------------------------
// My IOleInPlaceSite functions
//----------------------------------------------

HRESULT STDMETHODCALLTYPE InPlace_QueryInterface(IOleInPlaceSite FAR* This, REFIID riid, LPVOID FAR* ppvObj)
{
	return(Site_QueryInterface((IOleClientSite *)((char *)This - sizeof(IOleClientSite)), riid, ppvObj));
}

HRESULT STDMETHODCALLTYPE InPlace_AddRef(IOleInPlaceSite FAR* This)
{
	return(1);
}

HRESULT STDMETHODCALLTYPE InPlace_Release(IOleInPlaceSite FAR* This)
{
	return(1);
}

HRESULT STDMETHODCALLTYPE InPlace_GetWindow(IOleInPlaceSite FAR* This, HWND FAR* lphwnd)
{
	*lphwnd = ((_IOleInPlaceSiteEx FAR*)This)->frame.window;
	return(S_OK);
}

HRESULT STDMETHODCALLTYPE InPlace_ContextSensitiveHelp(IOleInPlaceSite FAR* This, BOOL fEnterMode)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE InPlace_CanInPlaceActivate(IOleInPlaceSite FAR* This)
{
	// Tell the browser we can in place activate
	return(S_OK);
}

HRESULT STDMETHODCALLTYPE InPlace_OnInPlaceActivate(IOleInPlaceSite FAR* This)
{
	// Tell the browser we did it ok
	return(S_OK);
}

HRESULT STDMETHODCALLTYPE InPlace_OnUIActivate(IOleInPlaceSite FAR* This)
{
	return(S_OK);
}

HRESULT STDMETHODCALLTYPE InPlace_GetWindowContext(IOleInPlaceSite FAR* This, LPOLEINPLACEFRAME FAR* lplpFrame, LPOLEINPLACEUIWINDOW FAR* lplpDoc, LPRECT lprcPosRect, LPRECT lprcClipRect, LPOLEINPLACEFRAMEINFO lpFrameInfo)
{
	*lplpFrame = (LPOLEINPLACEFRAME)&((_IOleInPlaceSiteEx *)This)->frame;
	*lplpDoc = 0;

	lpFrameInfo->fMDIApp = FALSE;
	lpFrameInfo->hwndFrame = ((_IOleInPlaceFrameEx *)*lplpFrame)->window;
	lpFrameInfo->haccel = 0;
	lpFrameInfo->cAccelEntries = 0;

	return(S_OK);
}

HRESULT STDMETHODCALLTYPE InPlace_Scroll(IOleInPlaceSite FAR* This, SIZE scrollExtent)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE InPlace_OnUIDeactivate(IOleInPlaceSite FAR* This, BOOL fUndoable)
{
	return(S_OK);
}

HRESULT STDMETHODCALLTYPE InPlace_OnInPlaceDeactivate(IOleInPlaceSite FAR* This)
{
	return(S_OK);
}

HRESULT STDMETHODCALLTYPE InPlace_DiscardUndoState(IOleInPlaceSite FAR* This)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE InPlace_DeactivateAndUndo(IOleInPlaceSite FAR* This)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE InPlace_OnPosRectChange(IOleInPlaceSite FAR* This, LPCRECT lprcPosRect)
{
	IOleObject			*browserObject;
	IOleInPlaceObject	*inplace;

	browserObject = *((IOleObject **)((char *)This - sizeof(IOleObject *) - sizeof(IOleClientSite)));
	if (!browserObject->lpVtbl->QueryInterface(browserObject, (IID *)&IID_IOleInPlaceObject, (void**)&inplace)) {
		inplace->lpVtbl->SetObjectRects(inplace, lprcPosRect, lprcPosRect);
	}

	return(S_OK);
}

//----------------------------------------------
// My IOleInPlaceFrame functions
//----------------------------------------------

HRESULT STDMETHODCALLTYPE Frame_QueryInterface(IOleInPlaceFrame FAR* This, REFIID riid, LPVOID FAR* ppvObj)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Frame_AddRef(IOleInPlaceFrame FAR* This)
{
	return(1);
}

HRESULT STDMETHODCALLTYPE Frame_Release(IOleInPlaceFrame FAR* This)
{
	return(1);
}

HRESULT STDMETHODCALLTYPE Frame_GetWindow(IOleInPlaceFrame FAR* This, HWND FAR* lphwnd)
{
	*lphwnd = ((_IOleInPlaceFrameEx *)This)->window;
	return(S_OK);
}

HRESULT STDMETHODCALLTYPE Frame_ContextSensitiveHelp(IOleInPlaceFrame FAR* This, BOOL fEnterMode)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Frame_GetBorder(IOleInPlaceFrame FAR* This, LPRECT lprectBorder)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Frame_RequestBorderSpace(IOleInPlaceFrame FAR* This, LPCBORDERWIDTHS pborderwidths)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Frame_SetBorderSpace(IOleInPlaceFrame FAR* This, LPCBORDERWIDTHS pborderwidths)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Frame_SetActiveObject(IOleInPlaceFrame FAR* This, IOleInPlaceActiveObject *pActiveObject, LPCOLESTR pszObjName)
{
	return(S_OK);
}

HRESULT STDMETHODCALLTYPE Frame_InsertMenus(IOleInPlaceFrame FAR* This, HMENU hmenuShared, LPOLEMENUGROUPWIDTHS lpMenuWidths)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Frame_SetMenu(IOleInPlaceFrame FAR* This, HMENU hmenuShared, HOLEMENU holemenu, HWND hwndActiveObject)
{
	return(S_OK);
}

HRESULT STDMETHODCALLTYPE Frame_RemoveMenus(IOleInPlaceFrame FAR* This, HMENU hmenuShared)
{
	return E_NOTIMPL;
}

HRESULT STDMETHODCALLTYPE Frame_SetStatusText(IOleInPlaceFrame FAR* This, LPCOLESTR pszStatusText)
{
	return(S_OK);
}

HRESULT STDMETHODCALLTYPE Frame_EnableModeless(IOleInPlaceFrame FAR* This, BOOL fEnable)
{
	return(S_OK);
}

HRESULT STDMETHODCALLTYPE Frame_TranslateAccelerator(IOleInPlaceFrame FAR* This, LPMSG lpmsg, WORD wID)
{
	return E_NOTIMPL;
}

//------------------------------------------------------------ PRIVATE FUNCTIONS

/* Implements the functionality of "Back", "Forward", "Home", "Search",
 * "Refresh", and "Stop" buttons.
 */

static BOOL DoPageAction(PWBOBJ pwbo, DWORD action, PVOID pvResult)
{
	IWebBrowser2	*webBrowser2;
	IOleObject		*browserObject;

	if(!pwbo)
		return FALSE;

	// Retrieve the browser object's pointer we stored in our window's GWL_USERDATA when
	// we initially attached the browser object to this window.

	browserObject = *((IOleObject **)pwbo->lparams[0]);

	// We want to get the base address (ie, a pointer) to the IWebBrowser2 object embedded within the browser
	// object, so we can call some of the functions in the former's table.

	if(!browserObject->lpVtbl->QueryInterface(browserObject, (IID *)&IID_IWebBrowser2, (void**)&webBrowser2)) {

		// Ok, now the pointer to our IWebBrowser2 object is in 'webBrowser2', and so its VTable is
		// webBrowser2->lpVtbl.

		// Call the desired function

		switch(action) {

			case WEBPAGE_GOBACK:
				webBrowser2->lpVtbl->GoBack(webBrowser2);
				break;

			case WEBPAGE_GOFORWARD:
				webBrowser2->lpVtbl->GoForward(webBrowser2);
				break;

			case WEBPAGE_GOHOME:
				webBrowser2->lpVtbl->GoHome(webBrowser2);
				break;

			case WEBPAGE_SEARCH:
				webBrowser2->lpVtbl->GoSearch(webBrowser2);
				break;

			case WEBPAGE_REFRESH:
				webBrowser2->lpVtbl->Refresh(webBrowser2);
				break;

			case WEBPAGE_STOP:
				webBrowser2->lpVtbl->Stop(webBrowser2);
				break;

			case WEBPAGE_BUSY:
				webBrowser2->lpVtbl->get_Busy(webBrowser2, pvResult);
				break;
		}

		// Release the IWebBrowser2 object.

		webBrowser2->lpVtbl->Release(webBrowser2);
	}
	return TRUE;
}

/* Called to detach the browser object from our host window, and free its
 * resources, right before we destroy our window.
 */

static BOOL UnEmbedBrowserObject(HWND hwnd)
{
	IOleObject	**browserHandle;
	IOleObject	*browserObject;

	PWBOBJ pwbo;

	pwbo = wbGetWBObj(hwnd);

//	if(NULL != (browserHandle = (IOleObject **)GetWindowLong(hwnd, GWL_USERDATA))) {
	browserHandle = ((IOleObject **)pwbo->lparams[0]);
	if(browserHandle) {
		browserObject = *browserHandle;
		browserObject->lpVtbl->Close(browserObject, OLECLOSE_NOSAVE);
		browserObject->lpVtbl->Release(browserObject);
//		SetWindowLong(hwnd, GWL_USERDATA, 0);
		pwbo->lparams[0] = 0;
	}
	return TRUE;
}

/* Resizes the browser object for the specified window to the specified
 * width and height.
 */

static void ResizeBrowser(HWND hwnd, DWORD width, DWORD height)
{
	IWebBrowser2	*webBrowser2;
	IOleObject		*browserObject;
	PWBOBJ pwbo;

	pwbo = wbGetWBObj(hwnd);
	if(!pwbo)
		return;

	browserObject = *((IOleObject **)pwbo->lparams[0]);
//	browserObject = *((IOleObject **)GetWindowLong(hwnd, GWL_USERDATA));

	if(!browserObject->lpVtbl->QueryInterface(browserObject, (IID *)&IID_IWebBrowser2, (void**)&webBrowser2)) {
		webBrowser2->lpVtbl->put_Width(webBrowser2, width);
		webBrowser2->lpVtbl->put_Height(webBrowser2, height);
		webBrowser2->lpVtbl->Release(webBrowser2);
	}
}

// Message handler for the window that hosts the browser control.

LRESULT CALLBACK BrowserWndProc(HWND hwnd, UINT uMsg, WPARAM wParam, LPARAM lParam)
{
	switch(uMsg) {

		case WM_SIZE:
			ResizeBrowser(hwnd, LOWORD(lParam), HIWORD(lParam));
			return 0;

//		case WM_CREATE:
//			if(EmbedBrowserObject(hwnd))
//				return -1;
//			return 0;

		case WM_DESTROY:
			UnEmbedBrowserObject(hwnd);
			return TRUE;
	}

	return DefWindowProc(hwnd, uMsg, wParam, lParam);
}

//------------------------------------------------------------------ END OF FILE
