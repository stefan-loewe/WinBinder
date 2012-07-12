<?php

/*******************************************************************************

WINBINDER - form editor PHP file (generated automatically)

*******************************************************************************/

// Control identifiers

if(!defined('IDC_TABCONTROL1020')) define('IDC_TABCONTROL1020', 1020);
if(!defined('IDC_PUSHBUTTON1001')) define('IDC_PUSHBUTTON1001', 1001);
if(!defined('IDC_IMAGEBUTTON1002')) define('IDC_IMAGEBUTTON1002', 1002);
if(!defined('IDC_INVISIBLEAREA1003')) define('IDC_INVISIBLEAREA1003', 1003);
if(!defined('IDC_CHECKBOX1004')) define('IDC_CHECKBOX1004', 1004);
if(!defined('IDC_RADIOBUTTON1005')) define('IDC_RADIOBUTTON1005', 1005);
if(!defined('IDC_HYPERLINK1006')) define('IDC_HYPERLINK1006', 1006);
if(!defined('IDC_EDITBOX1008')) define('IDC_EDITBOX1008', 1008);
if(!defined('IDC_RTFEDITBOX1009')) define('IDC_RTFEDITBOX1009', 1009);
if(!defined('IDC_LISTBOX1011')) define('IDC_LISTBOX1011', 1011);
if(!defined('IDC_COMBOBOX1013')) define('IDC_COMBOBOX1013', 1013);
if(!defined('IDC_SPINNER1014')) define('IDC_SPINNER1014', 1014);
if(!defined('IDC_SCROLLBAR1015')) define('IDC_SCROLLBAR1015', 1015);
if(!defined('IDC_SLIDER1016')) define('IDC_SLIDER1016', 1016);
if(!defined('IDC_GAUGE1017')) define('IDC_GAUGE1017', 1017);
if(!defined('IDC_CALENDAR1018')) define('IDC_CALENDAR1018', 1018);
if(!defined('IDC_HTMLCONTROL1019')) define('IDC_HTMLCONTROL1019', 1019);
if(!defined('IDC_TREEVIEW1021')) define('IDC_TREEVIEW1021', 1021);
if(!defined('IDC_LISTVIEW1022')) define('IDC_LISTVIEW1022', 1022);

// Create window

$winmain = wb_create_window(null, AppWindow, '(Empty Form)', WBC_CENTER, WBC_CENTER, 593, 484, 0x00001000, 1234);

// Insert controls

$tab = wb_create_control($winmain, TabControl, 'Tab1,Tab2,Tab3', 10, 215, 195, 85, IDC_TABCONTROL1020, 0x00000000, 0, 0);
wb_create_control($winmain, PushButton, 'PushButton1', 10, 15, 100, 25, IDC_PUSHBUTTON1001, 0x00000000, 0, 0);
$ctrl = $ctrl = wb_create_control($winmain, ImageButton, 'ImageButton2', 120, 15, 90, 60, IDC_IMAGEBUTTON1002, 0x00000000, 0, 0);
$img = wb_load_image('resources\symb_imagebutton.bmp');
  wb_set_image($ctrl, $img, NOCOLOR, 0, 0);
  wb_destroy_image($img);wb_create_control($winmain, InvisibleArea, 'InvisibleArea3', 220, 15, 70, 60, IDC_INVISIBLEAREA1003, 0x00000000, 0, 0);
wb_create_control($winmain, CheckBox, 'CheckBox4', 300, 20, 120, 15, IDC_CHECKBOX1004, 0x00000000, 0, 0);
wb_create_control($winmain, RadioButton, 'RadioButton5', 300, 40, 120, 15, IDC_RADIOBUTTON1005, 0x00000000, 0, 0);
wb_create_control($winmain, HyperLink, 'HyperLink6', 405, 25, 165, 15, IDC_HYPERLINK1006, 0x00000880, 12582912, 0);
wb_create_control($winmain, Label, 'Label7', 10, 50, 100, 15, 0, 0x00000000, 0, 0);
wb_create_control($winmain, EditBox, 'EditBox8', 220, 225, 200, 20, IDC_EDITBOX1008, 0x00000000, 0, 0);
wb_create_control($winmain, RTFEditBox, 'RTFEditBox9', 300, 65, 120, 140, IDC_RTFEDITBOX1009, 0x00000000, 0, 0);
wb_create_control($winmain, Frame, 'Frame10', 10, 75, 280, 130, 0, 0x00000000, 0, 0);
$ctrl = wb_create_control($winmain, ListBox, 'ListBox11', 25, 95, 120, 95, IDC_LISTBOX1011, 0x00000000, 0, 0);
wb_set_text($ctrl, explode(',', 'ListBox11'));
$ctrl = wb_create_control($winmain, ComboBox, 'ComboBox12', 155, 95, 120, 95, IDC_COMBOBOX1013, 0x00000000, 0, 0);
wb_set_text($ctrl, explode(',', 'ComboBox12'));
wb_create_control($winmain, Spinner, 'Spinner13', 440, 410, 20, 20, IDC_SPINNER1014, 0x00000000, 0, 0);
$ctrl = wb_create_control($winmain, ScrollBar, 'ScrollBar14', 435, 305, 140, 20, IDC_SCROLLBAR1015, 0x00000000, 0, 0);
wb_set_value($ctrl, 0);
$ctrl = wb_create_control($winmain, Slider, 'Slider15', 430, 380, 145, 20, IDC_SLIDER1016, 0x00000000, 50, 0);
wb_set_value($ctrl, 50);
$ctrl = wb_create_control($winmain, Gauge, 'Gauge16', 435, 345, 140, 20, IDC_GAUGE1017, 0x00000000, 50, 0);
wb_set_value($ctrl, 50);
wb_create_control($winmain, Calendar, 'Calendar17', 220, 263, 200, 167, IDC_CALENDAR1018, 0x00000008, 0, 0);
wb_create_control($winmain, HTMLControl, 'HTMLControl18', 435, 215, 140, 80, IDC_HTMLCONTROL1019, 0x00000008, 0, 0);
wb_create_control($winmain, TreeView, 'TreeView20', 425, 55, 150, 150, IDC_TREEVIEW1021, 0x00000000, 0, 0);
$ctrl = wb_create_control($winmain, ListView, 'Field 1,Field 2,Field3', 10, 310, 195, 120, IDC_LISTVIEW1022, 0x00000000, 0, 0);
wb_set_text($ctrl, 'Field 1,Field 2,Field3');

// End controls

?>