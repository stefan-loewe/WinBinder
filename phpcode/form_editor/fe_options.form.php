<?php

/*******************************************************************************

WINBINDER - form editor PHP file (generated automatically)

*******************************************************************************/

// Control identifiers

if(!defined('IDC_AUTOPHP')) define('IDC_AUTOPHP', 2001);
if(!defined('IDC_WIREFRAME')) define('IDC_WIREFRAME', 2002);
if(!defined('IDC_SHOWGRID')) define('IDC_SHOWGRID', 2003);
if(!defined('IDC_GRID')) define('IDC_GRID', 2004);
if(!defined('IDC_CONSTRUCTIONLINES')) define('IDC_CONSTRUCTIONLINES', 2005);
if(!defined('IDC_GHOST')) define('IDC_GHOST', 2006);
if(!defined('IDC_SHOWHEX')) define('IDC_SHOWHEX', 2007);
if(!defined('IDC_FILEPATH')) define('IDC_FILEPATH', 2008);
if(!defined('IDC_BROWSE')) define('IDC_BROWSE', 2009);
if(!defined('IDC_AUTOPATH')) define('IDC_AUTOPATH', 2010);

// Create window

$wb->winoptions = wb_create_window($wb->mainwin, ModalDialog, 'Options', WBC_CENTER, WBC_CENTER, 363, 384, 0x00000001, 0);

// Insert controls

wb_create_control($wb->winoptions, Label, 'The settings below apply to all projects.', 20, 10, 325, 20, 0, 0x00000080, 0, 0);
wb_create_control($wb->winoptions, Frame, 'General options', 10, 30, 335, 45, 0, 0x00000000, 0, 0);
wb_create_control($wb->winoptions, Frame, 'Display options', 10, 80, 335, 147, 0, 0x00000000, 0, 0);
wb_create_control($wb->winoptions, Frame, 'Default path for project files', 10, 235, 335, 82, 0, 0x00000000, 0, 0);
wb_create_control($wb->winoptions, CheckBox, '&Export PHP code automatically when saving', 20, 50, 325, 15, IDC_AUTOPHP, 0x00000000, 0, 0);
wb_create_control($wb->winoptions, CheckBox, '&Wireframe display', 20, 100, 315, 15, IDC_WIREFRAME, 0x00000000, 0, 0);
wb_create_control($wb->winoptions, CheckBox, 'Sho&w grid', 20, 120, 315, 15, IDC_SHOWGRID, 0x00000000, 0, 0);
wb_create_control($wb->winoptions, CheckBox, 'Snap to &grid', 20, 140, 315, 15, IDC_GRID, 0x00000000, 0, 0);
wb_create_control($wb->winoptions, CheckBox, 'Show &construction lines in some classes', 20, 160, 315, 15, IDC_CONSTRUCTIONLINES, 0x00000000, 0, 0);
wb_create_control($wb->winoptions, CheckBox, '&Leave "ghost" while moving controls', 20, 180, 315, 15, IDC_GHOST, 0x00000000, 0, 0);
wb_create_control($wb->winoptions, CheckBox, 'Display control values in &hexadecimal', 20, 200, 315, 15, IDC_SHOWHEX, 0x00000000, 0, 0);
wb_create_control($wb->winoptions, EditBox, '', 20, 280, 275, 20, IDC_FILEPATH, 0x00000000, 0, 0);
wb_create_control($wb->winoptions, PushButton, '...', 305, 280, 30, 20, IDC_BROWSE, 0x00000000, 0, 0);
wb_create_control($wb->winoptions, CheckBox, '&Update path automatically when opening or saving', 20, 255, 315, 15, IDC_AUTOPATH, 0x00000000, 0, 0);
wb_create_control($wb->winoptions, PushButton, 'OK', 80, 325, 90, 25, IDOK, 0x00000000, 0, 0);
wb_create_control($wb->winoptions, PushButton, 'Cancel', 180, 325, 90, 25, IDCANCEL, 0x00000000, 0, 0);

// End controls

?>