<?php

// Control identifiers

if(!defined("IDD_DLG101")) define("IDD_DLG101", 101);
if(!defined("IDC_CONTROLS")) define("IDC_CONTROLS", 1002);
if(!defined("IDC_LEFTSPINNER")) define("IDC_LEFTSPINNER", 1003);
if(!defined("IDC_TOPSPINNER")) define("IDC_TOPSPINNER", 1004);
if(!defined("IDC_WIDTHSPINNER")) define("IDC_WIDTHSPINNER", 1005);
if(!defined("IDC_HEIGHTSPINNER")) define("IDC_HEIGHTSPINNER", 1006);
if(!defined("IDC_CLASS")) define("IDC_CLASS", 1007);
if(!defined("IDC_TOP")) define("IDC_TOP", 1008);
if(!defined("IDC_LEFT")) define("IDC_LEFT", 1009);
if(!defined("IDC_ID")) define("IDC_ID", 1010);
if(!defined("IDC_WIDTH")) define("IDC_WIDTH", 1011);
if(!defined("IDC_HEIGHT")) define("IDC_HEIGHT", 1012);
if(!defined("IDC_GRID")) define("IDC_GRID", 1015);
if(!defined("IDC_WIREFRAME")) define("IDC_WIREFRAME", 1016);
if(!defined("IDC_CAPTION")) define("IDC_CAPTION", 1017);
if(!defined("IDC_STYLES")) define("IDC_STYLES", 1019);
if(!defined("IDC_CLASSES")) define("IDC_CLASSES", 1020);
if(!defined("IDC_VALUE")) define("IDC_VALUE", 1021);
if(!defined("IDC_VALUESPINNER")) define("IDC_VALUESPINNER", 1023);
if(!defined("IDC_HEX")) define("IDC_HEX", 1024);
if(!defined("IDC_VALUEAUX")) define("IDC_VALUEAUX", 1025);

// Create window

$wb->mainwin = wb_create_window(NULL, 101, "WinBinder Form Editor", WBC_CENTER, WBC_CENTER, 732, 555, 1, 0);

// Insert controls

wb_create_control($wb->mainwin, Frame, "", 4, 66, 166, 3, 0, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($wb->mainwin, Frame, "", 0, 27, 1498, 3, 0, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($wb->mainwin, Frame, "", 4, 435, 166, 3, -1, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($wb->mainwin, Frame, "", 4, 495, 166, 3, -1, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($wb->mainwin, Label, "Class:", 5, 240, 35, 16, -1, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE, 0);
wb_create_control($wb->mainwin, Label, "Caption:", 4, 263, 41, 16, -1, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE, 0);
wb_create_control($wb->mainwin, Label, "Id:", 5, 287, 31, 16, -1, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE, 0);
wb_create_control($wb->mainwin, Label, "Left:", 1, 448, 26, 16, -1, WBC_VISIBLE | WBC_ENABLED | WBC_RIGHT, 0);
wb_create_control($wb->mainwin, Label, "Width:", 83, 448, 34, 16, -1, WBC_VISIBLE | WBC_ENABLED | WBC_RIGHT, 0);
wb_create_control($wb->mainwin, Label, "Top:", 2, 471, 25, 16, -1, WBC_VISIBLE | WBC_ENABLED | WBC_RIGHT, 0);
wb_create_control($wb->mainwin, Label, "Height:", 79, 471, 41, 16, -1, WBC_VISIBLE | WBC_ENABLED | WBC_RIGHT, 0);
wb_create_control($wb->mainwin, Label, "Value:", 4, 310, 40, 16, 1022, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE, 0);
wb_create_control($wb->mainwin, CheckBox, "&Wireframe", 100, 37, 70, 21, IDC_WIREFRAME, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($wb->mainwin, CheckBox, "Snap to &grid", 4, 37, 85, 21, IDC_GRID, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($wb->mainwin, CheckBox, "&Hex", 134, 313, 40, 16, IDC_HEX, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($wb->mainwin, TreeView, "", 4, 76, 166, 154, IDC_CONTROLS, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($wb->mainwin, ListView, "", 4, 334, 166, 91, IDC_STYLES, WBC_VISIBLE | WBC_ENABLED | WBC_SORT | WBC_SINGLE, 0);
wb_create_control($wb->mainwin, ComboBox, "", 47, 235, 125, 146, IDC_CLASSES, WBC_VISIBLE | WBC_ENABLED | WBC_READONLY, 0);
wb_create_control($wb->mainwin, EditBox, "", 47, 235, 122, 21, IDC_CLASS, WBC_VISIBLE | WBC_DISABLED | WBC_READONLY, 0);
wb_create_control($wb->mainwin, EditBox, "", 46, 260, 125, 21, IDC_CAPTION, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($wb->mainwin, EditBox, "", 46, 284, 125, 21, IDC_ID, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($wb->mainwin, EditBox, "", 28, 445, 35, 19, IDC_LEFT, WBC_VISIBLE | WBC_ENABLED | WBC_NUMBER, 0);
wb_create_control($wb->mainwin, Spinner, "", 64, 445, 16, 19, IDC_LEFTSPINNER, WBC_VISIBLE | WBC_ENABLED | WBC_GROUP, 0);
wb_create_control($wb->mainwin, EditBox, "", 28, 469, 35, 19, IDC_TOP, WBC_VISIBLE | WBC_ENABLED | WBC_NUMBER, 0);
wb_create_control($wb->mainwin, Spinner, "", 64, 469, 16, 19, IDC_TOPSPINNER, WBC_VISIBLE | WBC_ENABLED | WBC_GROUP, 0);
wb_create_control($wb->mainwin, EditBox, "", 119, 445, 35, 19, IDC_WIDTH, WBC_VISIBLE | WBC_ENABLED | WBC_NUMBER, 0);
wb_create_control($wb->mainwin, Spinner, "", 154, 445, 16, 19, IDC_WIDTHSPINNER, WBC_VISIBLE | WBC_ENABLED | WBC_GROUP, 0);
wb_create_control($wb->mainwin, EditBox, "", 119, 469, 35, 19, IDC_HEIGHT, WBC_VISIBLE | WBC_ENABLED | WBC_NUMBER, 0);
wb_create_control($wb->mainwin, Spinner, "", 154, 469, 16, 19, IDC_HEIGHTSPINNER, WBC_VISIBLE | WBC_ENABLED | WBC_GROUP, 0);
wb_create_control($wb->mainwin, EditBox, "", 46, 308, 68, 21, IDC_VALUE, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($wb->mainwin, EditBox, "", 4, 505, 40, 16, IDC_VALUEAUX, WBC_VISIBLE | WBC_ENABLED | WBC_READONLY | WBC_NUMBER, 0);
wb_create_control($wb->mainwin, Spinner, "", 115, 308, 11, 22, IDC_VALUESPINNER, WBC_VISIBLE | WBC_ENABLED | WBC_GROUP, 0);

// End controls

?>