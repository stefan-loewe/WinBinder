<?php

/*******************************************************************************

WINBINDER - form editor PHP file (generated automatically)

*******************************************************************************/

// Control identifiers

if(!defined('IDC_PRINTERLIST')) define('IDC_PRINTERLIST', 1005);
if(!defined('IDC_PAPERSIZE')) define('IDC_PAPERSIZE', 1007);
if(!defined('IDC_PRINTALL')) define('IDC_PRINTALL', 1008);
if(!defined('IDC_PRINTPAGES')) define('IDC_PRINTPAGES', 1009);
if(!defined('IDC_STARTPAGE')) define('IDC_STARTPAGE', 1010);
if(!defined('IDC_ENDPAGE')) define('IDC_ENDPAGE', 1012);
if(!defined('IDC_QUALITY')) define('IDC_QUALITY', 1014);
if(!defined('IDC_WIDTH')) define('IDC_WIDTH', 1015);
if(!defined('IDC_HEIGHT')) define('IDC_HEIGHT', 1016);
if(!defined('IDC_UNITS')) define('IDC_UNITS', 1018);
if(!defined('IDC_COPIES')) define('IDC_COPIES', 1019);
if(!defined('IDC_SPINNER1002')) define('IDC_SPINNER1002', 1002);
if(!defined('IDC_PORTRAIT')) define('IDC_PORTRAIT', 1020);
if(!defined('IDC_LANDSCAPE')) define('IDC_LANDSCAPE', 1021);
if(!defined('IDOK')) define('IDOK', 1022);
if(!defined('IDCLOSE')) define('IDCLOSE', 1023);

// Create window

$winmain = wb_create_window(null, AppWindow, 'Print', WBC_CENTER, WBC_CENTER, 409, 337, 0x00000000, 0);

// Insert controls

wb_create_control($winmain, Frame, 'Printer', 15, 10, 370, 80, 0, 0x00000000, 0, 0);
wb_create_control($winmain, Frame, 'Print range', 15, 95, 180, 90, 0, 0x00000000, 0, 0);
wb_create_control($winmain, Frame, 'Paper size', 205, 95, 180, 90, 0, 0x00000000, 0, 0);
wb_create_control($winmain, Frame, 'Number of copies', 15, 190, 180, 65, 0, 0x00000000, 0, 0);
wb_create_control($winmain, Frame, 'Orientation', 205, 190, 180, 65, 0, 0x00000000, 0, 0);
wb_create_control($winmain, ComboBox, '', 95, 30, 280, 110, IDC_PRINTERLIST, 0x00000040, 0, 0);
wb_create_control($winmain, Label, 'Name:', 40, 30, 50, 15, 0, 0x00000020, 0, 0);
wb_create_control($winmain, ComboBox, '', 215, 115, 160, 85, IDC_PAPERSIZE, 0x00000040, 0, 0);
wb_create_control($winmain, RadioButton, 'All', 30, 125, 135, 15, IDC_PRINTALL, 0x00080000, 1, 0);
wb_create_control($winmain, RadioButton, 'Pages', 30, 155, 50, 15, IDC_PRINTPAGES, 0x00000000, 0, 0);
wb_create_control($winmain, EditBox, '1', 80, 153, 35, 20, IDC_STARTPAGE, 0x00000400, 0, 0);
wb_create_control($winmain, Label, 'to', 120, 155, 14, 15, 0, 0x00000000, 0, 0);
wb_create_control($winmain, EditBox, '1', 135, 153, 35, 20, IDC_ENDPAGE, 0x00000000, 0, 0);
wb_create_control($winmain, Label, 'Quality:', 35, 60, 55, 15, 0, 0x00000020, 0, 0);
wb_create_control($winmain, ComboBox, '', 95, 60, 135, 85, IDC_QUALITY, 0x00000040, 0, 0);
wb_create_control($winmain, EditBox, '', 215, 150, 55, 20, IDC_WIDTH, 0x00000040, 0, 0);
wb_create_control($winmain, EditBox, '', 295, 150, 55, 20, IDC_HEIGHT, 0x00000040, 0, 0);
wb_create_control($winmain, Label, 'x', 277, 152, 11, 15, 0, 0x00000800, 0, 0);
wb_create_control($winmain, Label, 'units', 355, 152, 25, 15, IDC_UNITS, 0x00000000, 0, 0);
wb_create_control($winmain, EditBox, '1', 35, 215, 90, 20, IDC_COPIES, 0x00000000, 0, 0);
wb_create_control($winmain, Spinner, 'Spinner21', 125, 215, 20, 20, IDC_SPINNER1002, 0x00080000, 0, 0);
wb_create_control($winmain, RadioButton, 'Portrait', 220, 210, 120, 15, IDC_PORTRAIT, 0x00000000, 1, 0);
wb_create_control($winmain, RadioButton, 'Landscape', 220, 230, 120, 15, IDC_LANDSCAPE, 0x00000000, 0, 0);
wb_create_control($winmain, PushButton, 'Print', 100, 270, 90, 25, IDOK, 0x00000000, 0, 0);
wb_create_control($winmain, PushButton, 'Close', 210, 270, 90, 25, IDCLOSE, 0x00000000, 0, 0);

// End controls

?>