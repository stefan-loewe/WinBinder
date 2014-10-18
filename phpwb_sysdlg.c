/*******************************************************************************

 WINBINDER - The native Windows binding for PHP for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 ZEND wrapper for common dialog boxes

*******************************************************************************/

//----------------------------------------------------------------- DEPENDENCIES

#include "phpwb.h"
//#include <shlobj.h>
//#include <stdio.h>

//----------------------------------------------------------- EXPORTED FUNCTIONS

ZEND_FUNCTION(wbtemp_sys_dlg_open)
{
    long pwboParent = (long)NULL;
	char *title = "", *filter = "", *path = "";
	TCHAR  *Current, firstString[MAX_PATH];
	int title_len = 0, filter_len = 0, path_len = 0, numCount=0, fileCount = 0;
	long style;
	TCHAR szFile[MAX_PATH] = TEXT("");

	char *file = 0;
	int file_len = 0;

	TCHAR *szTitle = 0;
	TCHAR *szFilter = 0;
	TCHAR *szPath = 0;
	TCHAR thisOne[MAX_PATH], fullPath[MAX_PATH * 2];				

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "l|sssl", &pwboParent, &title, &title_len, &filter, &filter_len, &path, &path_len, &style) == FAILURE)
		return;

	if(pwboParent && !wbIsWBObj((void *)pwboParent, TRUE))
		RETURN_NULL()

	szTitle = Utf82WideChar(title, title_len);
	szFilter = Utf82WideChar(filter, filter_len);
	szPath = Utf82WideChar(path, path_len);

	wbSysDlgOpen((PWBOBJ)pwboParent, szTitle, szFilter, szPath, szFile, style);
	
	
	
	if(*szFile) {
		if (!BITTEST(style, WBC_MULTISELECT))
		{
				file = WideChar2Utf8(szFile, &file_len);
				RETURN_STRINGL(file, file_len, TRUE);
		}
		//file = WideChar2Utf8(szFile, &file_len);
		array_init(return_value);
		wcscpy(thisOne,"");
		// [...]
		// szFile is a string separated by \0 and \0\0 for multiple entries
		for (Current = szFile; numCount < MAX_PATH;  numCount++) 
		{												
			wcscpy(thisOne,Current);
			Current += wcslen(Current);
			numCount += wcslen(Current);
			   						
			if (wcslen(thisOne) > 0)
			{	if (fileCount == 0)
				{
					wcscpy(firstString,thisOne);
				}
				else
				{	
					wcscpy(fullPath, firstString);
					if (fullPath[wcslen(fullPath)] != "\\")
					{
						wcscat(fullPath, "\\");
					}
					wcscat(fullPath, thisOne);
					file = WideChar2Utf8(fullPath, &file_len);
					add_next_index_string(return_value, file, 1);
				}
				fileCount++;
				
				
			}
			Current++;	
			numCount++;
			wcscpy(thisOne,Current);
		
			if (Current[0] == '\0')
			{
				if (fileCount == 1) 
				{
					file = WideChar2Utf8(firstString, &file_len);
					add_next_index_string(return_value, file, 1);
				}
				return;
				

			}			
		}			
	} else
		RETURN_STRING("", TRUE)
}

ZEND_FUNCTION(wbtemp_sys_dlg_save)
{
    long pwboParent = (long)NULL;
	char *title = "", *filter = "", *path = "", *file = "", *defext = "";
	int title_len = 0, filter_len = 0, path_len = 0, file_len = 0, defext_len = 0;
	TCHAR szFile[MAX_PATH] = TEXT("");
	TCHAR* szDefExt = 0;

	TCHAR *szTitle = 0;
	TCHAR *szFilter = 0;
	TCHAR *szPath = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "l|sssss", &pwboParent, &title, &title_len, &filter, &filter_len, &path, &path_len, &file, &file_len, &defext, &defext_len) == FAILURE)
		return;

	if(pwboParent && !wbIsWBObj((void *)pwboParent, TRUE))
		RETURN_NULL()

	if(*file)
//		strcpy(szFile, file);
		Utf82WideCharCopy(file, file_len, szFile, file_len);
	if(*defext)
//		strcpy(szDefExt, defext);
		szDefExt = Utf82WideChar(defext, defext_len);

	szTitle = Utf82WideChar(title, title_len);
	szFilter = Utf82WideChar(filter, filter_len);
	szPath = Utf82WideChar(path, path_len);

	wbSysDlgSave((PWBOBJ)pwboParent, szTitle, szFilter, szPath, szFile, szDefExt);

	if(*szFile) {
		file = WideChar2Utf8(szFile, &file_len);
		RETURN_STRING(file, TRUE)
	} else
		RETURN_STRING("", TRUE)
}

ZEND_FUNCTION(wb_sys_dlg_path)
{
    long pwboParent = (long)NULL;
	char *title = "", *path = "";
	int title_len = 0, path_len = 0;
	TCHAR szSelPath[MAX_PATH] = TEXT("");

	TCHAR *szTitle = 0;
	TCHAR *szPath = 0;
	char *selPath = 0;
	int sel_len = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "l|ss", &pwboParent, &title, &title_len, &path, &path_len) == FAILURE)
		return;

	if(pwboParent && !wbIsWBObj((void *)pwboParent, TRUE))
		RETURN_NULL()

	szTitle = Utf82WideChar(title, title_len);
	szPath = Utf82WideChar(path, path_len);

	wbSysDlgPath((PWBOBJ)pwboParent, szTitle, szPath, szSelPath);

	if(*szSelPath) {
		selPath = WideChar2Utf8(szSelPath, &sel_len);
		RETURN_STRING(selPath, TRUE)
	}
	else
		RETURN_STRING("", TRUE)
}

ZEND_FUNCTION(wb_sys_dlg_color)
{
    long pwboParent = (long)NULL;
    long color = NOCOLOR;
	char *title = "";
	int title_len = 0;

	TCHAR *szTitle = 0;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "l|sl", &pwboParent, &title, &title_len, &color) == FAILURE)
		return;

	if(pwboParent && !wbIsWBObj((void *)pwboParent, TRUE))
		RETURN_NULL()

	szTitle = Utf82WideChar(title, title_len);
	RETURN_LONG(wbSysDlgColor((PWBOBJ)pwboParent, szTitle, (COLORREF)color));
}

/*
ZEND_FUNCTION(wb_sys_dlg_font)
{
    long pwbparent = (long)NULL;
	char *title = "";
	char *name = "";
	int height = 0, color = 0, flags = 0;
	int title_len = 0, name_len = 0;
	FONT font;

    if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
	  "|lsslll", &pwbparent, &title, &title_len, &name, &name_len, &height, &color, &flags) == FAILURE)
		return;

	if(pwbo && !wbIsWBObj((void *)pwbo, TRUE))
		RETURN_NULL()

	font.pszName = name;
	font.nHeight = height;
	font.color = color;
	font.dwFlags = flags;
	RETURN_LONG(wbSysDlgFont((PWBOBJ)pwbparent, title, &font));
}
*/

//------------------------------------------------------------------ END OF FILE
