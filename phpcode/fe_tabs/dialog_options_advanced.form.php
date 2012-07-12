<?php

/*******************************************************************************

WINBINDER - form editor PHP file (generated automatically)

*******************************************************************************/

// Control identifiers

if(!defined('IDC_LANGUAGE')) define('IDC_LANGUAGE', 1301);
if(!defined('IDC_TOOLTIPS')) define('IDC_TOOLTIPS', 1302);
if(!defined('IDC_CONFIRMATIONS')) define('IDC_CONFIRMATIONS', 1303);
if(!defined('IDC_ALLANIMATIONS')) define('IDC_ALLANIMATIONS', 1304);

// Insert controls

wb_create_control($maintab, Frame, 'Language options', 15, 10, 275, 95, 0, 0x00000000, 0, 1);
wb_create_control($maintab, Frame, 'Interface options', 15, 115, 275, 115, 0, 0x00000000, 0, 1);
wb_create_control($maintab, Label, '&Language:', 30, 35, 160, 15, 0, 0x00000000, 0, 1);
wb_create_control($maintab, ComboBox, '', 30, 65, 235, 210, IDC_LANGUAGE, 0x00000040, 0, 1);
wb_create_control($maintab, CheckBox, 'Show &tooltips', 30, 140, 255, 15, IDC_TOOLTIPS, 0x00000000, 1, 1);
wb_create_control($maintab, CheckBox, '&Confirmations', 30, 170, 255, 15, IDC_CONFIRMATIONS, 0x00000000, 1, 1);
wb_create_control($maintab, CheckBox, '&Show all animations', 30, 200, 255, 15, IDC_ALLANIMATIONS, 0x00000000, 0, 1);

// End controls

?>