<?php

// Control identifiers

if(!defined("IDD_DIALOG1001")) define("IDD_DIALOG1001", 2001);
if(!defined("ID_MOVEUP")) define("ID_MOVEUP", 2008);
if(!defined("ID_MOVEDOWN")) define("ID_MOVEDOWN", 2009);
if(!defined("ID_NAME")) define("ID_NAME", 2113);
if(!defined("ID_NEWITEM")) define("ID_NEWITEM", 2115);
if(!defined("ID_DELETEITEM")) define("ID_DELETEITEM", 2116);
if(!defined("ID_SETITEM")) define("ID_SETITEM", 2117);
if(!defined("ID_SEVERITYLIST")) define("ID_SEVERITYLIST", 2112);

// Create window

$dlg = wb_create_window($parent, 2, "Edit Severity Levels", WBC_CENTER, WBC_CENTER, 337, 295, 0, 0);

// Insert controls

$_tmp_ctrl_ = wb_create_control($dlg, PushButton, "up_arrow.bmp", 178, 65, 20, 21, ID_MOVEUP, WBC_VISIBLE | WBC_ENABLED, 0);
wb_set_image($_tmp_ctrl_, 'C:\@desenv\WinBinder\phpcode\todo/../resources/up_arrow.bmp', GREEN); unset($_tmp_ctrl_);
$_tmp_ctrl_ = wb_create_control($dlg, PushButton, "dn_arrow.bmp", 205, 65, 20, 21, ID_MOVEDOWN, WBC_VISIBLE | WBC_ENABLED, 0);
wb_set_image($_tmp_ctrl_, 'C:\@desenv\WinBinder\phpcode\todo/../resources/dn_arrow.bmp', GREEN); unset($_tmp_ctrl_);
wb_create_control($dlg, EditBox, "", 178, 8, 146, 21, ID_NAME, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($dlg, PushButton, "&Close", 250, 238, 74, 24, IDCANCEL, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($dlg, PushButton, "&New", 4, 238, 74, 24, ID_NEWITEM, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($dlg, PushButton, "&Delete", 88, 238, 74, 24, ID_DELETEITEM, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($dlg, PushButton, "&Set", 169, 238, 74, 24, ID_SETITEM, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($dlg, ListBox, "", 4, 6, 166, 224, ID_SEVERITYLIST, WBC_VISIBLE | WBC_ENABLED, 0);

// End controls

?>