<?php

/*******************************************************************************

WINBINDER - form editor PHP file (generated automatically)

*******************************************************************************/

// Control identifiers

if(!defined('IDC_MAINTAB')) define('IDC_MAINTAB', 1001);

// Create window

$options_dlg = wb_create_window(null, AppWindow, 'A dialog box with tabs', WBC_CENTER, WBC_CENTER, 341, 397, 0x00000001, 0);

// Insert controls

$tab = wb_create_control($options_dlg, TabControl, 'General,Advanced', 10, 10, 310, 305, IDC_MAINTAB, 0x00000000, 0, 0);
wb_create_control($options_dlg, PushButton, 'OK', 65, 330, 90, 25, IDOK, 0x00000000, 0, 0);
wb_create_control($options_dlg, PushButton, 'Cancel', 170, 330, 90, 25, IDCANCEL, 0x00000000, 0, 0);

// End controls

?>