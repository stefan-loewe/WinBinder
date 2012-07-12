<?php

// Control identifiers

if(!defined("IDD_DLG2001")) define("IDD_DLG2001", 2001);
if(!defined("IDC_COMBOBOX2010")) define("IDC_COMBOBOX2010", 2010);
if(!defined("IDC_LISTBOX2018")) define("IDC_LISTBOX2018", 2018);
if(!defined("IDC_LISTVIEW2027")) define("IDC_LISTVIEW2027", 2027);
if(!defined("IDC_EDIT2064")) define("IDC_EDIT2064", 2064);
if(!defined("IDC_LISTVIEW2067")) define("IDC_LISTVIEW2067", 2067);
if(!defined("IDC_LISTLINE")) define("IDC_LISTLINE", 2068);
if(!defined("IDC_UPDOWN2069")) define("IDC_UPDOWN2069", 2069);

// Create window

wbtemp_create_item($maintab, "Test list");

// Insert controls

wb_create_control($maintab, ListView, "", 4, 6, 325, 159, IDC_LISTVIEW2027, WBC_VISIBLE | WBC_ENABLED | WBC_SORT | WBC_LINES | WBC_CHECKBOXES | WBC_SINGLE, 0, 0);
wb_create_control($maintab, ComboBox, "", 340, 4, 184, 195, IDC_COMBOBOX2010, WBC_VISIBLE | WBC_ENABLED | WBC_READONLY, 0, 0);
wb_create_control($maintab, ListBox, "", 455, 29, 70, 136, IDC_LISTBOX2018, WBC_VISIBLE | WBC_ENABLED, 0, 0);
wb_create_control($maintab, EditBox, "", 4, 175, 265, 66, IDC_EDIT2064, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE | WBC_READONLY, 0, 0);
wb_create_control($maintab, ListView, "", 280, 175, 245, 71, IDC_LISTVIEW2067, WBC_VISIBLE | WBC_ENABLED | WBC_SINGLE, 0, 0);
wb_create_control($maintab, EditBox, "0", 340, 45, 35, 21, IDC_LISTLINE, WBC_VISIBLE | WBC_ENABLED | WBC_NUMBER, 0, 0);
wb_create_control($maintab, Spinner, "", 378, 45, 16, 21, IDC_UPDOWN2069, WBC_VISIBLE | WBC_ENABLED, 0, 0);

// End controls


// Control identifiers

if(!defined("IDD_DLG4001")) define("IDD_DLG4001", 4001);
if(!defined("IDC_PROGRESSBAR4007")) define("IDC_PROGRESSBAR4007", 4007);
if(!defined("IDC_SLIDER4011")) define("IDC_SLIDER4011", 4011);
if(!defined("IDC_GROUPBOX4012")) define("IDC_GROUPBOX4012", 4012);
if(!defined("IDC_RADIOBUTTON4014")) define("IDC_RADIOBUTTON4014", 4014);
if(!defined("IDC_RADIOBUTTON4015")) define("IDC_RADIOBUTTON4015", 4015);
if(!defined("IDC_STATIC4019")) define("IDC_STATIC4019", 4019);
if(!defined("IDC_HSCROLL4026")) define("IDC_HSCROLL4026", 4026);
if(!defined("IDC_UPDOWN4028")) define("IDC_UPDOWN4028", 4028);
if(!defined("IDC_EDIT4030")) define("IDC_EDIT4030", 4030);
if(!defined("IDC_CHECKBOX4034")) define("IDC_CHECKBOX4034", 4034);
if(!defined("IDC_RADIOBUTTON4016")) define("IDC_RADIOBUTTON4016", 4016);
if(!defined("IDC_STATIC4048")) define("IDC_STATIC4048", 4048);
if(!defined("IDC_CHECKBOX4049")) define("IDC_CHECKBOX4049", 4049);
if(!defined("IDC_VSCROLL4051")) define("IDC_VSCROLL4051", 4051);
if(!defined("IDC_EDIT4052")) define("IDC_EDIT4052", 4052);
if(!defined("IDC_LISTBOX4053")) define("IDC_LISTBOX4053", 4053);
if(!defined("IDC_COMBOBOX4054")) define("IDC_COMBOBOX4054", 4054);
if(!defined("IDC_LISTBOX4055")) define("IDC_LISTBOX4055", 4055);
if(!defined("IDC_HSCROLL4056")) define("IDC_HSCROLL4056", 4056);
if(!defined("IDC_EDIT4057")) define("IDC_EDIT4057", 4057);
if(!defined("IDC_EDIT4058")) define("IDC_EDIT4058", 4058);
if(!defined("IDC_EDIT4059")) define("IDC_EDIT4059", 4059);
if(!defined("IDC_EDIT4060")) define("IDC_EDIT4060", 4060);
if(!defined("IDC_STATIC4063")) define("IDC_STATIC4063", 4063);
if(!defined("IDC_STATIC4064")) define("IDC_STATIC4064", 4064);
if(!defined("IDC_EDIT4065")) define("IDC_EDIT4065", 4065);
if(!defined("IDC_RADIOBUTTON4066")) define("IDC_RADIOBUTTON4066", 4066);
if(!defined("IDC_RADIOBUTTON4067")) define("IDC_RADIOBUTTON4067", 4067);
if(!defined("IDC_RADIOBUTTON4068")) define("IDC_RADIOBUTTON4068", 4068);
if(!defined("IDC_EDIT4069")) define("IDC_EDIT4069", 4069);
if(!defined("IDC_EDIT4071")) define("IDC_EDIT4071", 4071);

// Create window

wbtemp_create_item($maintab, "Test values");

// Insert controls

wb_create_control($maintab, Frame, "Group box", 4, 6, 140, 89, IDC_GROUPBOX4012, WBC_VISIBLE | WBC_ENABLED, 0, 1);
wb_create_control($maintab, RadioButton, "Opt. A&1", 10, 24, 59, 14, IDC_RADIOBUTTON4014, WBC_VISIBLE | WBC_ENABLED | WBC_GROUP, 0, 1);
wb_create_control($maintab, RadioButton, "Opt. A&2", 10, 48, 59, 14, IDC_RADIOBUTTON4015, WBC_VISIBLE | WBC_ENABLED, 0, 1);
wb_create_control($maintab, RadioButton, "Opt. A&3", 10, 73, 59, 14, IDC_RADIOBUTTON4016, WBC_VISIBLE | WBC_ENABLED, 0, 1);
wb_create_control($maintab, RadioButton, "Opt. &B1", 74, 24, 58, 14, IDC_RADIOBUTTON4066, WBC_VISIBLE | WBC_ENABLED | WBC_GROUP, 0, 1);
wb_create_control($maintab, RadioButton, "&Opt. B2", 74, 48, 58, 14, IDC_RADIOBUTTON4067, WBC_VISIBLE | WBC_ENABLED, 0, 1);
wb_create_control($maintab, RadioButton, "O&pt. B3", 74, 73, 58, 14, IDC_RADIOBUTTON4068, WBC_VISIBLE | WBC_ENABLED, 0, 1);
wb_create_control($maintab, Gauge, "IDC_PROGRESSBAR4007", 184, 35, 103, 14, IDC_PROGRESSBAR4007, WBC_VISIBLE | WBC_ENABLED, 0, 1);
wb_create_control($maintab, Slider, "IDC_SLIDER4011", 148, 68, 145, 26, IDC_SLIDER4011, WBC_VISIBLE | WBC_ENABLED, 0, 1);
wb_create_control($maintab, Label, "0", 148, 35, 26, 14, IDC_STATIC4019, WBC_VISIBLE | WBC_ENABLED | WBC_RIGHT, 0, 1);
wb_create_control($maintab, ScrollBar, "IDC_HSCROLL4026", 154, 9, 136, 16, IDC_HSCROLL4026, WBC_VISIBLE | WBC_ENABLED, 0, 1);
wb_create_control($maintab, EditBox, "0", 148, 104, 40, 19, IDC_EDIT4030, WBC_VISIBLE | WBC_ENABLED | WBC_NUMBER, 0, 1);
wb_create_control($maintab, Spinner, "", 190, 104, 16, 19, IDC_UPDOWN4028, WBC_VISIBLE | WBC_ENABLED, 0, 1);
wb_create_control($maintab, CheckBox, "Checkbox 1", 14, 105, 91, 14, IDC_CHECKBOX4034, WBC_VISIBLE | WBC_ENABLED, 0, 1);
wb_create_control($maintab, Label, "(Values)", 4, 204, 316, 26, IDC_STATIC4048, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE, 0, 1);
wb_create_control($maintab, CheckBox, "Checkbox 2", 14, 125, 91, 14, IDC_CHECKBOX4049, WBC_VISIBLE | WBC_ENABLED, 0, 1);
wb_create_control($maintab, ScrollBar, "", 298, 4, 16, 126, IDC_VSCROLL4051, WBC_VISIBLE | WBC_ENABLED, 0, 1);
wb_create_control($maintab, EditBox, "0", 235, 104, 40, 19, IDC_EDIT4052, WBC_VISIBLE | WBC_ENABLED | WBC_NUMBER, 0, 1);
wb_create_control($maintab, ListBox, "", 325, 4, 68, 52, IDC_LISTBOX4053, WBC_VISIBLE | WBC_ENABLED, 0, 1);
wb_create_control($maintab, ComboBox, "", 399, 4, 130, 195, IDC_COMBOBOX4054, WBC_VISIBLE | WBC_ENABLED | WBC_READONLY, 0, 1);
wb_create_control($maintab, ListBox, "", 399, 29, 130, 21, IDC_LISTBOX4055, WBC_VISIBLE | WBC_ENABLED, 0, 1);
wb_create_control($maintab, ScrollBar, "IDC_HSCROLL4056", 148, 130, 145, 16, IDC_HSCROLL4056, WBC_VISIBLE | WBC_ENABLED, 0, 1);
wb_create_control($maintab, EditBox, "Pack my box with five dozen liquor jugs", 4, 152, 155, 21, IDC_EDIT4057, WBC_VISIBLE | WBC_ENABLED, 0, 1);
wb_create_control($maintab, EditBox, "", 4, 175, 155, 21, IDC_EDIT4058, WBC_VISIBLE | WBC_ENABLED | WBC_READONLY, 0, 1);
wb_create_control($maintab, EditBox, "", 169, 152, 145, 21, IDC_EDIT4059, WBC_VISIBLE | WBC_ENABLED | WBC_MASKED, 0, 1);
wb_create_control($maintab, Label, "&Digits only:", 169, 180, 55, 21, IDC_STATIC4063, WBC_VISIBLE | WBC_ENABLED | WBC_RIGHT, 0, 1);
wb_create_control($maintab, EditBox, "", 226, 175, 89, 21, IDC_EDIT4060, WBC_VISIBLE | WBC_ENABLED | WBC_NUMBER, 0, 1);
wb_create_control($maintab, EditBox, "", 325, 61, 205, 55, IDC_EDIT4065, WBC_VISIBLE | WBC_DISABLED | WBC_MULTILINE, 0, 1);
wb_create_control($maintab, Label, "Drag me:", 193, 52, 59, 14, IDC_STATIC4064, WBC_VISIBLE | WBC_ENABLED | WBC_CENTER, 0, 1);
wb_create_control($maintab, EditBox, "", 325, 125, 205, 21, IDC_EDIT4069, WBC_VISIBLE | WBC_ENABLED, 0, 1);
wb_create_control($maintab, EditBox, "", 340, 154, 190, 81, IDC_EDIT4071, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE, 0, 1);

// End controls


// Control identifiers

if(!defined("IDD_DLG5001")) define("IDD_DLG5001", 5001);
if(!defined("IDC_GROUPBOX5012")) define("IDC_GROUPBOX5012", 5012);
if(!defined("IDC_TOOLBAR5025")) define("IDC_TOOLBAR5025", 5025);
if(!defined("IDD_DLG5027")) define("IDD_DLG5027", 5027);
if(!defined("IDC_SHAPE5056")) define("IDC_SHAPE5056", 5056);
if(!defined("IDC_SHAPE5057")) define("IDC_SHAPE5057", 5057);
if(!defined("IDC_SHAPE5058")) define("IDC_SHAPE5058", 5058);
if(!defined("IDC_STATIC5060")) define("IDC_STATIC5060", 5060);
if(!defined("IDC_SHAPE5061")) define("IDC_SHAPE5061", 5061);
if(!defined("IDC_STATIC5062")) define("IDC_STATIC5062", 5062);
if(!defined("IDC_STATIC5063")) define("IDC_STATIC5063", 5063);
if(!defined("IDC_BUTTON5068")) define("IDC_BUTTON5068", 5069);
if(!defined("IDC_SHAPE5065")) define("IDC_SHAPE5065", 5065);
if(!defined("IDC_SHAPE5066")) define("IDC_SHAPE5066", 5066);
if(!defined("IDC_SHAPE5067")) define("IDC_SHAPE5067", 5067);
if(!defined("IDC_BUTTON5068")) define("IDC_BUTTON5068", 5068);
if(!defined("IDC_STATIC5070")) define("IDC_STATIC5070", 5070);
if(!defined("IDC_STATIC5071")) define("IDC_STATIC5071", 5071);

// Create window

wbtemp_create_item($maintab, "Static controls");

// Insert controls

wb_create_control($maintab, Frame, "", 125, 8, 119, 68, IDC_SHAPE5058, WBC_VISIBLE | WBC_ENABLED, 0, 2);
wb_create_control($maintab, Frame, "Group box", 4, 4, 110, 71, IDC_GROUPBOX5012, WBC_VISIBLE | WBC_ENABLED, 0, 2);
wb_create_control($maintab, Frame, "", 325, 9, 130, 3, IDC_SHAPE5056, WBC_VISIBLE | WBC_ENABLED, 0, 2);
wb_create_control($maintab, Frame, "", 464, 9, 1, 61, IDC_SHAPE5057, WBC_VISIBLE | WBC_ENABLED, 0, 2);
$_tmp_ctrl_ = wb_create_control($maintab, Frame, "dn_arrow.bmp", 4, 139, 112, 45, IDC_STATIC5062, WBC_VISIBLE | WBC_ENABLED | WBC_BORDER | WBC_IMAGE | WBC_CENTER, 0, 2);
wb_set_image($_tmp_ctrl_, 'C:\@desenv\WinBinder\phpcode\examples/../resources/dn_arrow.bmp', GREEN); unset($_tmp_ctrl_);
wb_create_control($maintab, Label, "This text is centered.", 464, 79, 61, 35, IDC_STATIC5060, WBC_VISIBLE | WBC_ENABLED | WBC_CENTER, 0, 2);
wb_create_control($maintab, Label, "For a 'sunken' frame or label, use WinBinder style WBC_BORDER (WS_EX_STATICEDGE).", 4, 84, 181, 45, IDC_SHAPE5061, WBC_VISIBLE | WBC_ENABLED | WBC_BORDER | WBC_MULTILINE, 0, 2);
$_tmp_ctrl_ = wb_create_control($maintab, Frame, "hyper.ico", 124, 139, 112, 45, IDC_STATIC5063, WBC_VISIBLE | WBC_ENABLED | WBC_IMAGE | WBC_CENTER, 0, 2);
wb_set_image($_tmp_ctrl_, 'C:\@desenv\WinBinder\phpcode\examples/../resources/hyper.ico', GREEN); unset($_tmp_ctrl_);
wb_create_control($maintab, Label, "This label control wraps words if they do not fit in the availble space.", 4, 195, 130, 45, IDC_SHAPE5065, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE, 0, 2);
wb_create_control($maintab, Label, "This one does not wrap anything.", 139, 195, 151, 16, IDC_SHAPE5066, WBC_VISIBLE | WBC_ENABLED, 0, 2);
wb_create_control($maintab, Label, "This one uses ellipsis to display the text.", 139, 219, 110, 21, IDC_SHAPE5067, WBC_VISIBLE | WBC_ENABLED | WBC_ELLIPSIS, 0, 2);
wb_create_control($maintab, PushButton, "&Test", 364, 24, 76, 26, IDC_BUTTON5068, WBC_VISIBLE | WBC_ENABLED | WBC_BORDER, 0, 2);
wb_create_control($maintab, PushButton, "&Test", 280, 24, 76, 26, IDC_BUTTON5068, WBC_VISIBLE | WBC_ENABLED, 0, 2);
$_tmp_ctrl_ = wb_create_control($maintab, Frame, "up_arrow.bmp", 250, 134, 65, 50, IDC_STATIC5070, WBC_VISIBLE | WBC_ENABLED | WBC_IMAGE | WBC_CENTER, 0, 2);
wb_set_image($_tmp_ctrl_, 'C:\@desenv\WinBinder\phpcode\examples/../resources/up_arrow.bmp', GREEN); unset($_tmp_ctrl_);
wb_create_control($maintab, Label, "This text is right-aligned.", 370, 79, 85, 35, IDC_STATIC5071, WBC_VISIBLE | WBC_ENABLED | WBC_RIGHT, 0, 2);

// End controls
// Control identifiers

if(!defined("IDD_DLG6001")) define("IDD_DLG6001", 6001);
if(!defined("IDC_EDIT6002")) define("IDC_EDIT6002", 6002);
if(!defined("ID_LOWLEVEL")) define("ID_LOWLEVEL", 6003);
if(!defined("ID_RESETHANDLER")) define("ID_RESETHANDLER", 6004);

// Create window

wbtemp_create_item($maintab, "Low-level");

// Insert controls

wb_create_control($maintab, PushButton, "Run &Tests", 10, 14, 80, 30, ID_LOWLEVEL, WBC_VISIBLE | WBC_ENABLED, 0, 3);
wb_create_control($maintab, EditBox, "", 194, 9, 275, 216, IDC_EDIT6002, WBC_VISIBLE | WBC_ENABLED | WBC_MULTILINE | WBC_READONLY, 0, 3);
wb_create_control($maintab, PushButton, "Reset &Handler", 10, 50, 80, 30, ID_RESETHANDLER, WBC_VISIBLE | WBC_ENABLED, 0, 3);

// End controls

?>