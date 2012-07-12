<?php

/*******************************************************************************

WINBINDER - form editor PHP file (generated automatically)

*******************************************************************************/

// Control identifiers

if(!defined('IDC_EDITBOX1001')) define('IDC_EDITBOX1001', 1001);
if(!defined('IDC_EDITBOX1002')) define('IDC_EDITBOX1002', 1002);
if(!defined('IDC_EDITBOX1002')) define('IDC_EDITBOX1002', 1003);
if(!defined('MAKEID_1K1K')) define('MAKEID_1K1K', 1004);
if(!defined('MAKEID_2K2K')) define('MAKEID_2K2K', 1005);
if(!defined('MAKEID_2K2K')) define('MAKEID_2K2K', 1006);
if(!defined('IDC_RADIOBUTTON1009')) define('IDC_RADIOBUTTON1009', 1009);

// Create window

$winmain = wb_create_window(null, AppWindow, 'ID tests', WBC_CENTER, WBC_CENTER, 382, 470, 0x00000000, 0);

// Insert controls

wb_create_control($winmain, Frame, 'Null identifier', 20, 20, 140, 60, 0, 0x00000000, 0, 0);
wb_create_control($winmain, Label, 'Null identifier', 50, 45, 90, 15, 0, 0x00000000, 0, 0);
wb_create_control($winmain, EditBox, 'Valid ID (1001)', 20, 100, 185, 20, IDC_EDITBOX1001, 0x00000000, 0, 0);
wb_create_control($winmain, PushButton, 'OK', 240, 25, 90, 25, IDOK, 0x00000000, 0, 0);
wb_create_control($winmain, PushButton, 'Cancel', 240, 60, 90, 25, IDCANCEL, 0x00000000, 0, 0);
wb_create_control($winmain, EditBox, 'Valid ID (1002)', 20, 130, 185, 20, IDC_EDITBOX1002, 0x00000000, 0, 0);
wb_create_control($winmain, EditBox, 'Duplicate ID (1002)', 20, 160, 185, 20, IDC_EDITBOX1002, 0x00000000, 0, 0);
wb_create_control($winmain, CheckBox, 'Invalid ID (1k1k)', 20, 195, 185, 15, MAKEID_1K1K, 0x00000000, 0, 0);
wb_create_control($winmain, CheckBox, 'Invalid ID (2k2k)', 20, 220, 175, 15, MAKEID_2K2K, 0x00000000, 0, 0);
wb_create_control($winmain, CheckBox, 'Invalid ID (2k2k, duplicated)', 20, 245, 175, 15, MAKEID_2K2K, 0x00000000, 0, 0);
wb_create_control($winmain, RadioButton, 'Numeric ID (800)', 200, 200, 120, 15, 800, 0x00000000, 0, 0);
wb_create_control($winmain, RadioButton, 'Numeric ID (800, duplicated)', 200, 230, 155, 15, IDC_RADIOBUTTON1009, 0x00000000, 0, 0);

// End controls

?>