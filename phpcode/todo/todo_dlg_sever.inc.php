<?php

/*******************************************************************************

SEVERITY DIALOG

*******************************************************************************/

//-------------------------------------------------------------------- FUNCTIONS

function create_sever_dlg($parent)
{
	global $sever_table;

	// Remove the line below while not under development

	file_put_contents("dlg_sever.rc.php", "<?php\n\n" . parse_rc(file_get_contents(PATH_DATA . "dlg_sever.rc"),
	  '$dlg', '$parent', 'ModalDialog') . "\n?>");

	include("dlg_sever.rc.php");
	wb_set_handler($dlg, "process_sever");
	$sever_table = "severity";

	wb_set_enabled(wb_get_control($dlg, ID_SETITEM), false);
	wb_set_enabled(wb_get_control($dlg, ID_DELETEITEM), false);

	update_sever_controls($dlg);
	update_severs($dlg);
}

function process_sever($window, $id)
{
	global $mainwin, $sever_table;

	switch($id) {

		case ID_SEVERITYLIST:
			update_sever_controls($window);
			break;

		case ID_NEWITEM:
			$name = wb_get_text(wb_get_control($window, ID_NAME));
			db_edit_record($sever_table, 0, "name", array($name));
			update_severs($window);
			update_sever_controls($window);
			break;

		case ID_SETITEM:
			$name = wb_get_text(wb_get_control($window, ID_NAME));
			$itemlist = wb_get_control($window, ID_SEVERITYLIST);
			$id = db_get_id($sever_table, wb_get_selected($itemlist));
			db_edit_record($sever_table, $id, "name", array($name));
			update_severs($window);
			update_sever_controls($window);
			break;

		case ID_DELETEITEM:
			$itemlist = wb_get_control($window, ID_SEVERITYLIST);
			$selected = wb_get_selected($itemlist);
			$id = db_get_id($sever_table, $selected);
			db_delete_records($sever_table, db_get_id($sever_table, $selected));
			update_severs($window);
			update_sever_controls($window);
			break;

		case ID_MOVEUP:
			$itemlist = wb_get_control($window, ID_SEVERITYLIST);
			$selected = wb_get_selected($itemlist);
			$id = db_get_id($sever_table, $selected);
			db_swap_records($sever_table, db_get_id($sever_table, $selected),
				db_get_id($sever_table, $selected - 1));
			update_severs($window);
			update_sever_controls($window);
			break;

		case ID_MOVEDOWN:
			$itemlist = wb_get_control($window, ID_SEVERITYLIST);
			$selected = wb_get_selected($itemlist);
			$id = db_get_id($sever_table, $selected);
			db_swap_records($sever_table, db_get_id($sever_table, $selected),
				db_get_id($sever_table, $selected + 1));
			update_severs($window);
			update_sever_controls($window);
			break;

		case IDCANCEL:
		case IDCLOSE:
		case IDOK:
			wb_destroy_window($window);
			break;
	}
}

/* Update severity listing */

function update_severs($window)
{
	global $sever_table;

	$itemlist = wb_get_control($window, ID_SEVERITYLIST);
	wb_set_text($itemlist, db_get_data($sever_table, null, "name"));
}

/* Update controls */

function update_sever_controls($window)
{
	global $mainwin, $sever_table;

	$itemlist = wb_get_control($window, ID_SEVERITYLIST);

	$id = db_get_id($sever_table, wb_get_selected($itemlist));
	wb_set_text(wb_get_control($window, ID_NAME),
		db_get_data($sever_table, $id, "name"));

	$sel = wb_get_selected($itemlist);
	wb_set_enabled(wb_get_control($window, ID_SETITEM), $sel != -1);
	wb_set_enabled(wb_get_control($window, ID_DELETEITEM), $sel != -1);
}

//-------------------------------------------------------------------------- END

?>
