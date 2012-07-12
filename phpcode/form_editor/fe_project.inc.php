<?php

/*******************************************************************************

 WINBINDER - The native Windows binding for PHP for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 Form editor - Project dialog

*******************************************************************************/

function create_project_dlg($parent)
{
	global $wb;

	include("fe_project.form.php");

	// 	Read various settings

	foreach($wb->project_array as $var) {
		$var = strtolower($var);
		$ctrl = wb_get_control($wb->winproject, constant("IDC_" . strtoupper($var)));
		if(preg_match("/[\d\.\,]+/", $wb->form[$wb->currentform]->$var))
			wb_set_value($ctrl, $wb->form[$wb->currentform]->$var);
		else {
			if(wb_get_class($ctrl) != RadioButton && wb_get_class($ctrl) != CheckBox)
				wb_set_text($ctrl, $wb->form[$wb->currentform]->$var);
		}
	}

	// Disable / enable controls according to context

	wb_set_enabled(wb_get_control($wb->winproject, IDC_LOCPREFIX),
	  wb_get_value(wb_get_control($wb->winproject, IDC_LOCALIZE)));
	$val = wb_get_value(wb_get_control($wb->winproject, IDC_ISTABPAGE));
	wb_set_enabled(wb_get_control($wb->winproject, IDC_TABNUMBER), $val);
	wb_set_enabled(wb_get_control($wb->winproject, IDC_TABNUMBERSPINNER), $val);
	$hastext = (wb_get_text(wb_get_control($wb->winproject, IDC_CTRLVAR)) !== '');
	wb_set_enabled(wb_get_control($wb->winproject, IDC_STARTCTRLVAL), $hastext);

	wb_set_handler($wb->winproject, "process_project");
	wb_set_visible($wb->winproject, true);
	set_default_accel($wb->winproject, false);
}

function process_project($window, $id, $ctrl)
{
	global $wb;

	switch($id) {

		case IDC_ISTABPAGE:
			$val = wb_get_value($ctrl);
			wb_set_enabled(wb_get_control($wb->winproject, IDC_TABNUMBER), $val);
			wb_set_enabled(wb_get_control($wb->winproject, IDC_TABNUMBERSPINNER), $val);
			break;

		case IDC_CTRLVAR:
			$hastext = (wb_get_text($ctrl) !== '');
			wb_set_enabled(wb_get_control($wb->winproject, IDC_STARTCTRLVAL), $hastext);
			break;

		case IDC_LOCALIZE:
			wb_set_enabled(wb_get_control($wb->winproject, IDC_LOCPREFIX), wb_get_value($ctrl));
			break;

		case IDOK:

			// 	Set various settings

			foreach($wb->project_array as $var) {
				$var = strtolower($var);
				$ctrl = wb_get_control($wb->winproject, constant("IDC_" . strtoupper($var)));
				if(wb_get_class($ctrl) != RadioButton && wb_get_class($ctrl) != CheckBox)
					$wb->form[$wb->currentform]->$var = wb_get_text($ctrl);
				else
					$wb->form[$wb->currentform]->$var = wb_get_value($ctrl);
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
