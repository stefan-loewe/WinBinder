<?php

/*******************************************************************************

 WINBINDER - The native Windows binding for PHP for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 Form editor - Options dialog

*******************************************************************************/

function create_options_dlg($parent)
{
	global $wb;

	include("fe_options.form.php");

	// 	Read various settings

	foreach($wb->options_array as $var) {
		$var = strtolower($var);
		$ctrl = wb_get_control($wb->winoptions, constant("IDC_" . strtoupper($var)));
		if(preg_match("/[\d\.\,]+/", $wb->$var))
			wb_set_value($ctrl, $wb->$var);
		else {
			if(wb_get_class($ctrl) != RadioButton && wb_get_class($ctrl) != CheckBox)
				wb_set_text($ctrl, $wb->$var);
		}
	}
	wb_set_handler($wb->winoptions, "process_options");
	wb_set_visible($wb->winoptions, true);
	set_default_accel($wb->winoptions, false);
}

function process_options($window, $id, $ctrl)
{
	global $wb;

	switch($id) {

		case IDC_WIREFRAME:
		case IDC_GRID:
			wb_set_value(wb_get_control($wb->mainwin, $id), wb_get_value($ctrl));
			break;

		case IDC_BROWSE:
	 		$current = wb_get_text(wb_get_control($window, IDC_FILEPATH));
			$path = wb_sys_dlg_path($window, 'Select default path for project files:', $current);
			if($path)
				wb_set_text(wb_get_control($window, IDC_FILEPATH), $path);
			break;

		case IDOK:

			// 	Set various settings

			foreach($wb->options_array as $var) {
				$var = strtolower($var);
				$ctrl = wb_get_control($wb->winoptions, constant("IDC_" . strtoupper($var)));
				if(wb_get_class($ctrl) == CheckBox)
					$wb->$var = wb_get_value($ctrl);
				else
					$wb->$var = wb_get_text($ctrl);
			}

			// Fall-through

		case IDCANCEL:
			set_default_accel();
			wb_destroy_window($window);
			break;
	}
}

//-------------------------------------------------------------------------- END

?>
