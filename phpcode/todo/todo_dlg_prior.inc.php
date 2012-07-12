<?php

/*******************************************************************************

 WINBINDER - The native Windows binding for PHP for PHP

 Copyright © Hypervisual - see LICENSE.TXT for details
 Author: Rubem Pechansky (http://winbinder.org/contact.php)

 ToDo application - Priority dialog

*******************************************************************************/

//-------------------------------------------------------------------- FUNCTIONS

function create_prior_dlg($parent)
{
	global $prior_table;

	// Remove the line below while not under development

	file_put_contents("dlg_prior.rc.php", "<?php\n\n" . parse_rc(file_get_contents(PATH_DATA . "dlg_prior.rc"),
	  '$dlg', '$parent', 'ModalDialog') . "\n?>");

	include("dlg_prior.rc.php");
	wb_set_handler($dlg, "process_prior");
	$prior_table = "priority";

	wb_set_enabled(wb_get_control($dlg, ID_SETITEM), false);
	wb_set_enabled(wb_get_control($dlg, ID_DELETEITEM), false);

	update_prior_controls($dlg);
	update_priors($dlg);
}

function process_prior($window, $id)
{
	global $mainwin, $prior_table;

	switch($id) {

		case ID_PRIORITYLIST:
			update_prior_controls($window);
			break;

		case ID_NEWITEM:
			$name = wb_get_text(wb_get_control($window, ID_NAME));
			db_edit_record($prior_table, 0, "name", array($name));
			update_priors($window);
			update_prior_controls($window);
			break;

		case ID_SETITEM:
			$name = wb_get_text(wb_get_control($window, ID_NAME));
			$itemlist = wb_get_control($window, ID_PRIORITYLIST);
			$id = db_get_id($prior_table, wb_get_selected($itemlist));
			db_edit_record($prior_table, $id, "name", array($name));
			update_priors($window);
			update_prior_controls($window);
			break;

		case ID_DELETEITEM:
			$itemlist = wb_get_control($window, ID_PRIORITYLIST);
			$selected = wb_get_selected($itemlist);
			$id = db_get_id($prior_table, $selected);
			db_delete_records($prior_table, db_get_id($prior_table, $selected));
			update_priors($window);
			update_prior_controls($window);
			break;

		case ID_MOVEUP:
			$itemlist = wb_get_control($window, ID_PRIORITYLIST);
			$selected = wb_get_selected($itemlist);
			$id = db_get_id($prior_table, $selected);
			db_swap_records($prior_table, db_get_id($prior_table, $selected),
				db_get_id($prior_table, $selected - 1));
			update_priors($window);
			update_prior_controls($window);
			break;

		case ID_MOVEDOWN:
			$itemlist = wb_get_control($window, ID_PRIORITYLIST);
			$selected = wb_get_selected($itemlist);
			$id = db_get_id($prior_table, $selected);
			db_swap_records($prior_table, db_get_id($prior_table, $selected),
				db_get_id($prior_table, $selected + 1));
			update_priors($window);
			update_prior_controls($window);
			break;

		case IDCANCEL:
		case IDCLOSE:
		case IDOK:
			wb_destroy_window($window);
			break;
	}
}

/* Update priority listing */

function update_priors($window)
{
	global $prior_table;

	$itemlist = wb_get_control($window, ID_PRIORITYLIST);
	wb_set_text($itemlist, db_get_data($prior_table, null, "name"));
}

/* Update controls */

function update_prior_controls($window)
{
	global $mainwin, $prior_table;

	$itemlist = wb_get_control($window, ID_PRIORITYLIST);

	$id = db_get_id($prior_table, wb_get_selected($itemlist));
	wb_set_text(wb_get_control($window, ID_NAME),
		db_get_data($prior_table, $id, "name"));

	$sel = wb_get_selected($itemlist);
	wb_set_enabled(wb_get_control($window, ID_SETITEM), $sel != -1);
	wb_set_enabled(wb_get_control($window, ID_DELETEITEM), $sel != -1);
}

//------------------------------------------------------------------ END OF FILE

?>
