<?php

/*******************************************************************************

 WINBINDER - The native Windows binding for PHP for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 Form editor - Floating tool dialog with buttons for all control classes

*******************************************************************************/

//-------------------------------------------------------------------- FUNCTIONS

function create_classes_dlg($parent)
{
	global $wb, $classes_table;

	$nclasses = count($wb->classes);

	// Create window

	$pos = wb_get_position($parent);
	$width = 88;
	$height = 23 + 26 * ceil($nclasses / 3);
	$wb->classdlg = wb_create_window($parent, ToolDialog, "Controls",
		max(0, $pos[0] - $width),
		max(0, $pos[1]), $width, $height);
	wb_set_handler($wb->classdlg, "process_classes");

	// Create buttons, one for each control

	for($i = 0; $i < $nclasses; $i++) {
		$const = "IDC_" . strtoupper($wb->classes[$i][1]);
		$c = wb_create_control($wb->classdlg, ImageButton,
			array('', $wb->classes[$i][1]),
			4 + 25 * ($i % 3),
			4 + 25 * (int)($i / 3),
			24, 24,
			constant($const), 0);
		$img = wb_load_image(PATH_RESPVT . "ctrl_{$wb->classes[$i][1]}3.bmp");
		wb_set_image($c, $img, 0, 0, 3);
		wb_destroy_image($img);
	}
}

/* Window processing */

function process_classes($window, $id, $ctrl, $lparam1=0, $lparam2=0)
{
	global $wb, $mainwin, $classes_table;

	switch($id) {

		case IDCLOSE:

			$nclasses = count($wb->classes);
			for($i = 0; $i < $nclasses; $i++) {
				$const = "IDC_" . strtoupper($wb->classes[$i][1]);
				$c = wb_get_control($wb->classdlg, constant($const));
			}
			wb_destroy_window($window);
			wb_set_selected($wb->mainmenu, ID_TOOLBOX, false);
			$wb->classdlg = null;
			break;

		default:
			process_main($window, $id, $ctrl, $lparam1, $lparam2);
			break;

	}
}
//-------------------------------------------------------------------------- END

?>
