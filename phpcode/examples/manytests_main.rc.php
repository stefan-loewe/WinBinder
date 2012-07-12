<?php

// Control identifiers

if(!defined("IDD_DLG1001")) define("IDD_DLG1001", 1001);
if(!defined("ID_EXIT")) define("ID_EXIT", 1002);
if(!defined("IDC_TABCONTROL1003")) define("IDC_TABCONTROL1003", 1003);
if(!defined("ID_RESULT")) define("ID_RESULT", 1048);
if(!defined("IDC_TOOLBAR1025")) define("IDC_TOOLBAR1025", 1025);
if(!defined("IDD_DLG1027")) define("IDD_DLG1027", 1027);
if(!defined("ID_TREE")) define("ID_TREE", 801);
if(!defined("IDC_UPDOWN1027")) define("IDC_UPDOWN1027", 1027);
if(!defined("IDC_BUTTON1035")) define("IDC_BUTTON1035", 1035);
if(!defined("IDC_BUTTON1036")) define("IDC_BUTTON1036", 1036);
if(!defined("IDC_BUTTON1037")) define("IDC_BUTTON1037", 1037);
if(!defined("IDC_BUTTON1039")) define("IDC_BUTTON1039", 1039);
if(!defined("ID_GDI")) define("ID_GDI", 1047);
if(!defined("IDC_SLIDER1049")) define("IDC_SLIDER1049", 1049);
if(!defined("ID_TIME")) define("ID_TIME", 1050);
if(!defined("IDC_PROGRESSBAR1051")) define("IDC_PROGRESSBAR1051", 1051);
if(!defined("IDC_BUTTON1053")) define("IDC_BUTTON1053", 1053);

// Create window

$mainwin = wb_create_window(NULL, 1, "Sample WinBinder application", WBC_CENTER, WBC_CENTER, 555, 458, 529, 5120);

// Insert controls

wb_create_control($mainwin, TabControl, "", 4, 35, 540, 274, IDC_TABCONTROL1003, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, PushButton, "&Fechar", 494, 315, 50, 45, ID_EXIT, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, PushButton, "", 259, 318, 40, 40, IDC_BUTTON1035, WBC_VISIBLE | WBC_ENABLED, 0);
$_tmp_ctrl_ = wb_create_control($mainwin, PushButton, "hyper.ico", 304, 318, 40, 40, IDC_BUTTON1036, WBC_VISIBLE | WBC_ENABLED, 0);
wb_set_image($_tmp_ctrl_, 'C:\@desenv\WinBinder\phpcode\examples/../resources/hyper.ico', GREEN); unset($_tmp_ctrl_);
$_tmp_ctrl_ = wb_create_control($mainwin, PushButton, "ok.bmp", 349, 318, 40, 40, IDC_BUTTON1037, WBC_VISIBLE | WBC_ENABLED, 0);
wb_set_image($_tmp_ctrl_, 'C:\@desenv\WinBinder\phpcode\examples/../resources/ok.bmp', GREEN); unset($_tmp_ctrl_);
wb_create_control($mainwin, PushButton, "Zero", 214, 318, 40, 40, IDC_BUTTON1039, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, Label, "...", 346, 365, 202, 19, ID_GDI, WBC_VISIBLE | WBC_ENABLED | WBC_RIGHT, 0);
wb_create_control($mainwin, EditBox, "(Results)", 4, 315, 205, 45, ID_RESULT, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE, 0);
wb_create_control($mainwin, Slider, "", 393, 320, 91, 35, IDC_SLIDER1049, WBC_VISIBLE | WBC_ENABLED | WBC_LINES, 0);
wb_create_control($mainwin, Label, "Time:", 214, 365, 121, 16, ID_TIME, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE, 0);
wb_create_control($mainwin, Gauge, "IDC_PROGRESSBAR1051", 5, 365, 205, 14, IDC_PROGRESSBAR1051, WBC_VISIBLE | WBC_ENABLED, 0);

// End controls

?>