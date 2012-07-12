<?php

// Control identifiers

if(!defined("IDD_DIALOG1001")) define("IDD_DIALOG1001", 2001);
if(!defined("ID_ITEMLIST")) define("ID_ITEMLIST", 2106);
if(!defined("ID_MOVEUP")) define("ID_MOVEUP", 2008);
if(!defined("ID_MOVEDOWN")) define("ID_MOVEDOWN", 2009);
if(!defined("ID_TREE")) define("ID_TREE", 2112);
if(!defined("ID_DESCRIPTION")) define("ID_DESCRIPTION", 2114);
if(!defined("IDC_SHAPE2119")) define("IDC_SHAPE2119", 2119);
if(!defined("ID_FILTERTYPE")) define("ID_FILTERTYPE", 2124);
if(!defined("ID_FILTERTEXT")) define("ID_FILTERTEXT", 2121);
if(!defined("ID_FILTER")) define("ID_FILTER", 2123);
if(!defined("ID_FIELD")) define("ID_FIELD", 2126);
if(!defined("ID_FILTERPRESETS")) define("ID_FILTERPRESETS", 2128);

// Create window

$mainwin = wb_create_window(NULL, 1, "WinBinder: To Do", WBC_CENTER, WBC_CENTER, 627, 510, 17, 64);

// Insert controls

wb_create_control($mainwin, ListView, "", 211, 61, 378, 279, ID_ITEMLIST, WBC_VISIBLE | WBC_ENABLED | WBC_SORT | WBC_LINES | WBC_SINGLE, 0);
$_tmp_ctrl_ = wb_create_control($mainwin, PushButton, "up_arrow.bmp", 594, 61, 20, 21, ID_MOVEUP, WBC_VISIBLE | WBC_ENABLED, 0);
wb_set_image($_tmp_ctrl_, 'C:\@desenv\WinBinder\phpcode\todo/../resources/up_arrow.bmp', GREEN); unset($_tmp_ctrl_);
$_tmp_ctrl_ = wb_create_control($mainwin, PushButton, "dn_arrow.bmp", 594, 86, 20, 21, ID_MOVEDOWN, WBC_VISIBLE | WBC_ENABLED, 0);
wb_set_image($_tmp_ctrl_, 'C:\@desenv\WinBinder\phpcode\todo/../resources/dn_arrow.bmp', GREEN); unset($_tmp_ctrl_);
wb_create_control($mainwin, TreeView, "", 4, 35, 205, 399, ID_TREE, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, EditBox, "(Description)", 214, 351, 375, 82, ID_DESCRIPTION, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE | WBC_READONLY, 0);
wb_create_control($mainwin, Frame, "", 0, 27, 615, 3, IDC_SHAPE2119, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, EditBox, "", 453, 35, 112, 21, ID_FILTERTEXT, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, PushButton, "Fi&lter", 570, 35, 44, 21, ID_FILTER, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, ComboBox, "", 453, 35, 112, 195, ID_FILTERPRESETS, WBC_VISIBLE | WBC_ENABLED | WBC_READONLY, 0);
wb_create_control($mainwin, ComboBox, "", 310, 35, 137, 146, ID_FILTERTYPE, WBC_VISIBLE | WBC_ENABLED | WBC_READONLY, 0);
wb_create_control($mainwin, ComboBox, "", 212, 35, 92, 146, ID_FIELD, WBC_VISIBLE | WBC_ENABLED | WBC_READONLY, 0);

// End controls

?>