<?php

/*******************************************************************************

 WINBINDER - The native Windows binding for PHP for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 Form editor - Functions from the Windows API

*******************************************************************************/

//-------------------------------------------------------------------- CONSTANTS

define("GWL_STYLE",			-16);
define("SC_CLOSE", 			61536);
define("WS_MAXIMIZEBOX", 	0x10000);
define("WS_MINIMIZEBOX", 	0x20000);
define("WS_SYSMENU",		0x80000);

//-------------------------------------------------------------------- FUNCTIONS

function init_dlls()
{
	global $USER, $KERNEL, $GDI;

	// Most common Windows libraries

	$USER = wb_load_library("USER");
	$KERNEL = wb_load_library("KERNEL");
	$GDI = wb_load_library("GDI");

	// Declare constants related to Windows and WinBinder structures

	define("WBOBJ",			"Vhwnd/Vid/Vuclass/litem/lsubitem/Vstyle/Vparent/Vhandler/Vlparam/V8lparams/Vpbuffer");
	define("WBOBJ_RAW",		"V3l2V13");
	define("WBOBJ_SIZE",	72);

	define("IDC_ARROW",		32512);
	define("IDC_HAND", 		32649);
	define("IDC_CROSS",		32515);
	define("IDC_SIZEWE",	32644);
	define("IDC_SIZENS",	32645);
	define("IDC_SIZENWSE",	32642);
	define("IDC_SIZENESW",	32643);
	define("IDC_SIZE",		32640);
}

function SetMyCursor($parm)
{
	global $USER;
	static $pfn = null, $pfn2 = null;

	if($pfn === null)
		$pfn = wb_get_function_address("SetCursor", $USER);
	if($pfn2 === null)
		$pfn2 = wb_get_function_address("LoadCursor", $USER);

	$hcursor = wb_call_function($pfn2, array(0, $parm));
	wb_call_function($pfn, array($hcursor));
}

function GetWindowStyle($hwnd)
{
	global $USER;
	static $pfn = null;

	$wbobj = unpack(WBOBJ, wb_peek($hwnd, WBOBJ_SIZE));

	if($pfn === null)
		$pfn = wb_get_function_address("GetWindowLong", $USER);
	return wb_call_function($pfn, array($wbobj["hwnd"], GWL_STYLE));
}

function SetWindowStyle($hwnd, $style)
{
	global $USER;
	static $pfn = null;

	$wbobj = unpack(WBOBJ, wb_peek($hwnd, WBOBJ_SIZE));

	if($pfn === null)
		$pfn = wb_get_function_address("SetWindowLong", $USER);
	return wb_call_function($pfn, array($wbobj["hwnd"], GWL_STYLE, $style));
}

function EnableCloseButton($hwnd, $enable)
{
	global $USER;
	static $emi = null, $gsm = null;

	$wbobj = unpack(WBOBJ, wb_peek($hwnd, WBOBJ_SIZE));

	if($emi === null)
		$emi = wb_get_function_address("EnableMenuItem", $USER);
	if($gsm === null)
		$gsm = wb_get_function_address("GetSystemMenu", $USER);

	$sysmenu = wb_call_function($gsm, array($wbobj["hwnd"], false));
	return wb_call_function($emi, array($sysmenu, SC_CLOSE, !$enable));
}

//------------------------------------------------------------------ END OF FILE
