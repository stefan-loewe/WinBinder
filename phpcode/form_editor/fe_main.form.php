<?php

/*******************************************************************************

WINBINDER - form editor PHP file (generated automatically)

*******************************************************************************/

// Control identifiers

if(!defined('IDC_WIREFRAME')) define('IDC_WIREFRAME', 1001);
if(!defined('IDC_GRID')) define('IDC_GRID', 1002);
if(!defined('IDC_CONTROLS')) define('IDC_CONTROLS', 1003);
if(!defined('IDC_STYLES')) define('IDC_STYLES', 1004);
if(!defined('IDC_CLASSES')) define('IDC_CLASSES', 1005);
if(!defined('IDC_CLASS')) define('IDC_CLASS', 1006);
if(!defined('IDC_CAPTION')) define('IDC_CAPTION', 1007);
if(!defined('IDC_ID')) define('IDC_ID', 1008);
if(!defined('IDC_LEFT')) define('IDC_LEFT', 1009);
if(!defined('IDC_LEFTSPINNER')) define('IDC_LEFTSPINNER', 1010);
if(!defined('IDC_TOP')) define('IDC_TOP', 1011);
if(!defined('IDC_TOPSPINNER')) define('IDC_TOPSPINNER', 1012);
if(!defined('IDC_WIDTH')) define('IDC_WIDTH', 1013);
if(!defined('IDC_WIDTHSPINNER')) define('IDC_WIDTHSPINNER', 1014);
if(!defined('IDC_HEIGHT')) define('IDC_HEIGHT', 1015);
if(!defined('IDC_HEIGHTSPINNER')) define('IDC_HEIGHTSPINNER', 1016);
if(!defined('IDC_VALUE')) define('IDC_VALUE', 1017);
if(!defined('IDC_VALUEAUX')) define('IDC_VALUEAUX', 1018);
if(!defined('IDC_VALUESPINNER')) define('IDC_VALUESPINNER', 1019);

// Create window

$wb->mainwin = wb_create_window(null, ResizableWindow, 'WinBinder Form Editor', WBC_CENTER, WBC_CENTER, 185, 525, 0x00000001, 0);

// Insert controls

wb_create_control($wb->mainwin, Frame, '', 5, 30, 2048, 8, 0, 0x00000000, 0, 0);
wb_create_control($wb->mainwin, Frame, '', 5, 65, 165, 8, 0, 0x00000000, 0, 0);
wb_create_control($wb->mainwin, Frame, '', 5, 435, 165, 2, 0, 0x00000000, 0, 0);
wb_create_control($wb->mainwin, Frame, '', 5, 490, 165, 8, 0, 0x00000000, 0, 0);
wb_create_control($wb->mainwin, Label, 'Class:', 5, 235, 40, 20, 0, 0x00000020, 0, 0);
wb_create_control($wb->mainwin, Label, 'Caption:', 0, 260, 45, 20, 0, 0x00000020, 0, 0);
wb_create_control($wb->mainwin, Label, '&Id:', 5, 285, 40, 20, 0, 0x00000020, 0, 0);
wb_create_control($wb->mainwin, Label, '&Left:', 0, 440, 30, 20, 0, 0x00000020, 0, 0);
wb_create_control($wb->mainwin, Label, '&Width:', 80, 440, 40, 20, 0, 0x00000020, 0, 0);
wb_create_control($wb->mainwin, Label, '&Top:', 0, 465, 30, 20, 0, 0x00000020, 0, 0);
wb_create_control($wb->mainwin, Label, '&Height:', 80, 465, 40, 20, 0, 0x00000020, 0, 0);
wb_create_control($wb->mainwin, Label, '&Value:', 5, 310, 40, 20, 0, 0x00000020, 0, 0);
wb_create_control($wb->mainwin, CheckBox, '&Wireframe', 100, 40, 69, 15, IDC_WIREFRAME, 0x00000000, 0, 0);
wb_create_control($wb->mainwin, CheckBox, 'Snap to &grid', 5, 40, 90, 15, IDC_GRID, 0x00000000, 0, 0);
wb_create_control($wb->mainwin, TreeView, '', 5, 75, 165, 155, IDC_CONTROLS, 0x00000000, 0, 0);
wb_create_control($wb->mainwin, ListView, '', 5, 335, 165, 90, IDC_STYLES, 0x00000000, 0, 0);
wb_create_control($wb->mainwin, ComboBox, '', 50, 235, 120, 250, IDC_CLASSES, 0x00000040, 0, 0);
wb_create_control($wb->mainwin, EditBox, '', 50, 235, 120, 20, IDC_CLASS, 0x00000040, 0, 0);
wb_create_control($wb->mainwin, EditBox, '', 50, 260, 120, 20, IDC_CAPTION, 0x00000000, 0, 0);
wb_create_control($wb->mainwin, EditBox, '', 50, 285, 120, 20, IDC_ID, 0x00000000, 0, 0);
wb_create_control($wb->mainwin, EditBox, '', 30, 440, 35, 20, IDC_LEFT, 0x00000400, 0, 0);
wb_create_control($wb->mainwin, Spinner, '', 65, 440, 15, 20, IDC_LEFTSPINNER, 0x00080000, 0, 0);
wb_create_control($wb->mainwin, EditBox, '', 30, 465, 35, 20, IDC_TOP, 0x00000400, 0, 0);
wb_create_control($wb->mainwin, Spinner, '', 65, 465, 15, 20, IDC_TOPSPINNER, 0x00080000, 0, 0);
wb_create_control($wb->mainwin, EditBox, '', 120, 440, 35, 20, IDC_WIDTH, 0x00000400, 0, 0);
wb_create_control($wb->mainwin, Spinner, '', 155, 440, 15, 20, IDC_WIDTHSPINNER, 0x00080000, 0, 0);
wb_create_control($wb->mainwin, EditBox, '', 120, 465, 35, 20, IDC_HEIGHT, 0x00000400, 0, 0);
wb_create_control($wb->mainwin, Spinner, '', 155, 465, 15, 20, IDC_HEIGHTSPINNER, 0x00080000, 0, 0);
wb_create_control($wb->mainwin, EditBox, '', 50, 310, 105, 20, IDC_VALUE, 0x00000000, 0, 0);
wb_create_control($wb->mainwin, EditBox, '', 5, 305, 20, 10, IDC_VALUEAUX, 0x00000401, 0, 0);
wb_create_control($wb->mainwin, Spinner, '', 155, 310, 15, 20, IDC_VALUESPINNER, 0x00080000, 0, 0);

// End controls

?>