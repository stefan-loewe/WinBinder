<?php

// Control identifiers

if(!defined("IDD_DIALOG1001")) define("IDD_DIALOG1001", 2001);
if(!defined("ID_NAME")) define("ID_NAME", 2113);
if(!defined("ID_DESCRIPTION")) define("ID_DESCRIPTION", 2119);
if(!defined("ID_CATLIST")) define("ID_CATLIST", 2120);
if(!defined("ID_SEVERLIST")) define("ID_SEVERLIST", 2121);
if(!defined("ID_PRIORLIST")) define("ID_PRIORLIST", 2122);
if(!defined("IDC_STATIC2123")) define("IDC_STATIC2123", 2123);
if(!defined("IDC_STATIC2124")) define("IDC_STATIC2124", 2124);
if(!defined("IDC_STATIC2125")) define("IDC_STATIC2125", 2125);
if(!defined("IDC_STATIC2126")) define("IDC_STATIC2126", 2126);
if(!defined("IDC_STATIC2127")) define("IDC_STATIC2127", 2127);

// Create window

$dlg = wb_create_window($parent, 2, "Edit item", WBC_CENTER, WBC_CENTER, 332, 308, WBC_INVISIBLE, 0);

// Insert controls

wb_create_control($dlg, Label, "&Name:", 7, 4, 55, 16, IDC_STATIC2124, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE, 0);
wb_create_control($dlg, EditBox, "", 4, 22, 316, 21, ID_NAME, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($dlg, EditBox, "", 4, 63, 316, 126, ID_DESCRIPTION, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE, 0);
wb_create_control($dlg, Label, "&Category:", 7, 196, 55, 16, IDC_STATIC2123, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE, 0);
wb_create_control($dlg, ComboBox, "(Cat)", 4, 219, 140, 130, ID_CATLIST, WBC_VISIBLE | WBC_ENABLED | WBC_READONLY, 0);
wb_create_control($dlg, Label, "&Priority:", 151, 222, 44, 16, IDC_STATIC2127, WBC_VISIBLE | WBC_ENABLED | WBC_RIGHT, 0);
wb_create_control($dlg, ComboBox, "(Priority)", 200, 219, 119, 130, ID_PRIORLIST, WBC_VISIBLE | WBC_ENABLED | WBC_READONLY, 0);
wb_create_control($dlg, Label, "&Severity:", 139, 198, 55, 16, IDC_STATIC2126, WBC_VISIBLE | WBC_ENABLED | WBC_RIGHT, 0);
wb_create_control($dlg, ComboBox, "(Severity)", 200, 195, 119, 130, ID_SEVERLIST, WBC_VISIBLE | WBC_ENABLED | WBC_READONLY, 0);
wb_create_control($dlg, Label, "&Description:", 7, 45, 55, 16, IDC_STATIC2125, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE, 0);
wb_create_control($dlg, PushButton, "OK", 4, 251, 74, 24, IDOK, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($dlg, PushButton, "Cancel", 85, 251, 74, 24, IDCANCEL, WBC_VISIBLE | WBC_ENABLED, 0);

// End controls

?>