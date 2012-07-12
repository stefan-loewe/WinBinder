<?php

// Control identifiers

if(!defined("IDD_DLG1001")) define("IDD_DLG1001", 1001);
if(!defined("IDC_RESULT")) define("IDC_RESULT", 1002);
if(!defined("IDC_VERSION")) define("IDC_VERSION", 1007);
if(!defined("IDC_TYPE")) define("IDC_TYPE", 1009);
if(!defined("IDC_VERBOSE")) define("IDC_VERBOSE", 1010);
if(!defined("IDC_STATIC1008")) define("IDC_STATIC1008", 1008);
if(!defined("IDC_NOTES")) define("IDC_NOTES", 1012);
if(!defined("IDC_GROUPBOX1014")) define("IDC_GROUPBOX1014", 1014);
if(!defined("IDC_CHM")) define("IDC_CHM", 1015);
if(!defined("IDC_COPYFILES")) define("IDC_COPYFILES", 1018);
if(!defined("IDC_EXPORTTODO")) define("IDC_EXPORTTODO", 1019);
if(!defined("IDC_CHECKDLLS")) define("IDC_CHECKDLLS", 1020);
if(!defined("IDC_TGZ")) define("IDC_TGZ", 1021);
if(!defined("IDC_ZIP")) define("IDC_ZIP", 1022);
if(!defined("IDC_RELNOTES")) define("IDC_RELNOTES", 1023);
if(!defined("IDC_GO")) define("IDC_GO", 1024);
if(!defined("IDC_ABOUT")) define("IDC_ABOUT", 1025);
if(!defined("IDC_SAVEINI")) define("IDC_SAVEINI", 1026);
if(!defined("IDC_GROUPBOX1027")) define("IDC_GROUPBOX1027", 1027);
if(!defined("IDC_SETUP")) define("IDC_SETUP", 1034);
if(!defined("IDC_STATIC1029")) define("IDC_STATIC1029", 1029);
if(!defined("IDC_MIME")) define("IDC_MIME", 1030);
if(!defined("IDC_FLIST")) define("IDC_FLIST", 1031);
if(!defined("IDC_XML")) define("IDC_XML", 1032);
if(!defined("IDC_STATIC1033")) define("IDC_STATIC1033", 1033);
if(!defined("IDC_STATE")) define("IDC_STATE", 1035);
if(!defined("IDC_STATIC1036")) define("IDC_STATIC1036", 1036);
if(!defined("IDC_SAVERELEASE")) define("IDC_SAVERELEASE", 1037);
if(!defined("IDC_STATIC1038")) define("IDC_STATIC1038", 1038);
if(!defined("IDC_CLEAR")) define("IDC_CLEAR", 1039);

// Create window

$mainwin = wb_create_window(NULL, 3, "Make release", WBC_CENTER, WBC_CENTER, 609, 492, WBC_INVISIBLE, 0);

// Insert controls

wb_create_control($mainwin, Frame, "General Options", 5, 4, 265, 110, IDC_GROUPBOX1027, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, Label, "Project &State:", 29, 56, 70, 16, IDC_STATIC1036, WBC_VISIBLE | WBC_ENABLED | WBC_RIGHT, 0);
wb_create_control($mainwin, EditBox, "", 104, 53, 155, 21, IDC_STATE, WBC_VISIBLE | WBC_ENABLED | WBC_READONLY, 0);
wb_create_control($mainwin, Frame, "Build Options", 280, 4, 310, 406, IDC_GROUPBOX1014, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, EditBox, "", 5, 144, 265, 300, IDC_RESULT, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE, 0);
wb_create_control($mainwin, Label, "&Version / Release:", 10, 29, 89, 16, IDC_STATIC1008, WBC_VISIBLE | WBC_ENABLED | WBC_RIGHT, 0);
wb_create_control($mainwin, EditBox, "", 104, 27, 95, 21, IDC_VERSION, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, Label, "Release &Type:", 16, 82, 85, 16, IDC_STATIC1029, WBC_VISIBLE | WBC_ENABLED | WBC_RIGHT, 0);
wb_create_control($mainwin, ComboBox, "", 104, 79, 155, 146, IDC_TYPE, WBC_VISIBLE | WBC_ENABLED | WBC_READONLY, 0);
wb_create_control($mainwin, CheckBox, "&Verbose Output", 104, 120, 100, 21, IDC_VERBOSE, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, EditBox, "", 319, 248, 260, 152, IDC_NOTES, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE, 0);
wb_create_control($mainwin, CheckBox, "Copy Additional &Files to Web Site Folder", 295, 21, 235, 21, IDC_COPYFILES, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, CheckBox, "Compile &CHM (HTML Help) Manual", 295, 40, 230, 21, IDC_CHM, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, CheckBox, "Export T&o Do List as an HTML file", 295, 60, 235, 21, IDC_EXPORTTODO, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, CheckBox, "Generate &Zip Archive", 295, 79, 124, 21, IDC_ZIP, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, CheckBox, "Check if WinBinder &DLLs are Up-to-Date", 319, 125, 215, 21, IDC_CHECKDLLS, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, Label, "From file list:", 310, 105, 70, 16, IDC_STATIC1033, WBC_VISIBLE | WBC_ENABLED | WBC_RIGHT, 0);
wb_create_control($mainwin, EditBox, "", 389, 102, 115, 19, IDC_FLIST, WBC_VISIBLE | WBC_ENABLED | WBC_READONLY, 0);
wb_create_control($mainwin, CheckBox, "Create &XML Package Information (PECL)", 319, 144, 215, 21, IDC_XML, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, CheckBox, "Convert Zip to T&gz (PECL)", 319, 164, 196, 21, IDC_TGZ, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, CheckBox, "&MIME Encode Zip file (for e-mail)", 319, 183, 190, 21, IDC_MIME, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, CheckBox, "Save &Release Notes", 295, 222, 151, 22, IDC_RELNOTES, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, PushButton, "Go (F9)", 280, 419, 89, 26, IDC_GO, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, PushButton, "&Save Config.", 389, 419, 88, 26, IDC_SAVEINI, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, PushButton, "&About...", 498, 419, 91, 26, IDC_ABOUT, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, CheckBox, "Compile &Setup Application", 295, 203, 151, 22, IDC_SETUP, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, PushButton, "&Save", 205, 29, 55, 19, IDC_SAVERELEASE, WBC_VISIBLE | WBC_ENABLED, 0);
wb_create_control($mainwin, Label, "Output:", 7, 123, 49, 14, IDC_STATIC1038, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE, 0);
wb_create_control($mainwin, PushButton, "&Clear", 214, 120, 55, 19, IDC_CLEAR, WBC_VISIBLE | WBC_ENABLED, 0);

// End controls

?>